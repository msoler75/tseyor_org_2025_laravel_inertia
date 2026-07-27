<?php

namespace App\Console\Commands;

use App\Models\Comunicado;
use App\Pigmalion\ImageHasher;
use App\Pigmalion\Markdown;
use App\Pigmalion\StorageItem;
use App\Services\ImageDeduplicationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeduplicarImagenesComunicados extends Command
{
    protected $signature = 'comunicados:deduplicar-imagenes
                            {--dry-run : Preview changes without modifying anything}
                            {--color-threshold=90 : Color similarity %% for logos (first page seals)}
                            {--guide-threshold=95 : Color similarity %% for guide photos}
                            {--dhash-threshold=3 : dHash Hamming distance threshold}
                            {--limit=0 : Max comunicados to process (0 = all)}
                            {--latest : Process the latest comunicados instead of the first}';

    protected $description = 'Detecta y reemplaza imágenes duplicadas en comunicados por referencias canónicas.';

    /** @var array All canonicals with pre-computed pixels */
    protected array $canonicals = [];

    /** @var array Logo canonicals at 16x16 (square, for seal detection) */
    protected array $logoCanonicals = [];

    /** @var array Guide canonicals at 16xH (aspect-aware, with dimensions) */
    protected array $guideCanonicals = [];

    protected float $colorThreshold;
    protected float $guideThreshold;
    protected int $dhashThreshold;
    protected bool $dryRun = false;

    protected array $stats = [
        'revisados' => 0,
        'cambios' => 0,
        'duplicados' => 0,
        'eliminados' => 0,
        'errores' => 0,
        'dhash' => 0,
        'color_logo' => 0,
        'color_guia' => 0,
        'attrs_total' => 0,
        'attrs_usados' => 0,
    ];

    public function handle(): int
    {
        ini_set('memory_limit', '512M');
        $this->dryRun = (bool) $this->option('dry-run');
        $this->colorThreshold = (float) $this->option('color-threshold');
        $this->guideThreshold = (float) $this->option('guide-threshold');
        $this->dhashThreshold = (int) $this->option('dhash-threshold');

        $this->info('=== Deduplicador de imágenes de comunicados ===');
        $this->info(sprintf(
            'Modo: %s | dHash<=%d | logo>=%.0f%% (16x16) | guia>=%.0f%% (16xH)',
            $this->dryRun ? 'DRY-RUN' : 'REAL',
            $this->dhashThreshold,
            $this->colorThreshold,
            $this->guideThreshold
        ));
        $this->newLine();

        $this->loadCanonicals();

        if (empty($this->canonicals)) {
            $this->error('No se encontraron imágenes canónicas.');
            return self::FAILURE;
        }

        $this->info(sprintf(
            'Canónicas: %d total (%d logos 16x16 + %d guías 16xH, con dimensiones).',
            count($this->canonicals),
            count($this->logoCanonicals),
            count($this->guideCanonicals)
        ));
        $this->newLine();

        $this->processComunicados();
        $this->printSummary();

        return self::SUCCESS;
    }

    protected function loadCanonicals(): void
    {
        $this->info('Cargando imágenes canónicas...');

        $logoDir = new StorageItem('/almacen/medios/logos');
        $guiasDir = new StorageItem('/almacen/medios/guias');
        $guiasConNombreDir = new StorageItem('/almacen/medios/guias/con_nombre');

        $logos = [];
        $guias = [];

        if ($logoDir->directoryExists()) {
            foreach ($logoDir->files() as $fp) {
                $fn = basename($fp);
                if (preg_match('/^sello/i', $fn) && !preg_match('/\.svg$/i', $fn)) {
                    $sti = new StorageItem($fp);
                    $hash = ImageHasher::hash($sti->path);
                    if ($hash) {
                        $logos[] = ['path' => $fp, 'tipo' => 'logo', 'hash' => $hash];
                    }
                }
            }
        }

        if ($guiasDir->directoryExists()) {
            foreach ($guiasDir->files() as $fp) {
                $fn = basename($fp);
                if (preg_match('/\.(jpe?g|png)$/i', $fn)) {
                    $sti = new StorageItem($fp);
                    $hash = ImageHasher::hash($sti->path);
                    if ($hash) {
                        $guias[] = ['path' => $fp, 'tipo' => 'guia', 'hash' => $hash];
                    }
                }
            }
        }

        if ($guiasConNombreDir->directoryExists()) {
            foreach ($guiasConNombreDir->files() as $fp) {
                $fn = basename($fp);
                if (preg_match('/\.(jpe?g|png)$/i', $fn)) {
                    $sti = new StorageItem($fp);
                    $hash = ImageHasher::hash($sti->path);
                    if ($hash) {
                        $guias[] = ['path' => $fp, 'tipo' => 'guia_con_nombre', 'hash' => $hash];
                    }
                }
            }
        }

        $this->logoCanonicals = ImageHasher::precomputeCanonicals($logos, 16, 16);
        $this->guideCanonicals = ImageHasher::precomputeCanonicals($guias, 16, null);
        $this->canonicals = array_merge($this->logoCanonicals, $this->guideCanonicals);

        foreach ($this->canonicals as $c) {
            $grid = "{$c['grid_w']}x{$c['grid_h']}";
            $dims = "{$c['width']}x{$c['height']}";
            $this->line("  [{$c['tipo']}] {$c['path']} ($dims -> $grid)");
        }
    }

    protected function processComunicados(): void
    {
        $total = Comunicado::count();
        $limit = (int) $this->option('limit');
        if ($limit > 0 && $limit < $total) {
            $total = $limit;
            $this->info("Limitado a $total comunicados para pruebas.");
        }

        $bar = $this->output->createProgressBar($total);
        $bar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');

        $this->info("Procesando $total comunicados...");
        $this->newLine();
        $bar->start();

        $query = Comunicado::query();
        if ($this->option('latest')) {
            $query->orderBy('id', 'desc');
            $this->info('Procesando los mas recientes primero.');
        }

        $query->chunk(50, function ($comunicados) use ($bar, $limit) {
            foreach ($comunicados as $comunicado) {
                if ($limit > 0 && $this->stats['revisados'] >= $limit) {
                    return false;
                }

                try {
                    $this->processOne($comunicado);
                } catch (\Throwable $e) {
                    $msg = "Error #{$comunicado->id}: {$e->getMessage()}";
                    Log::error($msg);
                    $this->warn("  $msg");
                    $this->stats['errores']++;
                }
                $this->stats['revisados']++;
                $bar->setMessage("ID {$comunicado->id}");
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);
    }

    protected function processOne(Comunicado $comunicado): void
    {
        $texto = $comunicado->texto ?? '';
        $imagenField = $comunicado->imagen ?? '';

        $allPaths = array_unique(array_merge(
            Markdown::images($texto),
            !empty($imagenField) ? [$imagenField] : []
        ));

        $localPaths = [];
        foreach ($allPaths as $p) {
            if (strpos($p, '/almacen/medios/comunicados/') !== false) {
                $localPaths[] = $p;
            }
        }

        if (empty($localPaths)) {
            return;
        }

        $firstPageSet = $this->firstPageImageSet($texto);
        $imageAttrs = $this->extractImageAttributes($texto);
        $changes = [];

        foreach ($localPaths as $imgPath) {
            $attrDims = $imageAttrs[$imgPath] ?? null;

            if ($attrDims !== null) {
                $this->stats['attrs_total']++;
            }

            $sti = new StorageItem($imgPath);
            if (!$sti->exists()) {
                continue;
            }

            $match = $this->matchImage($sti->path, $imgPath, $firstPageSet, $attrDims);

            if ($match === null) {
                continue;
            }

            $repl = $match['path'];

            // Para logos en 1a pagina: siempre sello_tseyor_64.png
            if ($match['tipo'] === 'logo' && isset($firstPageSet[$imgPath])) {
                $repl = '/almacen/medios/logos/sello_tseyor_64.png';
            }

            // Para guias: agregar ?w= para que no queden enormes
            if ($match['tipo'] !== 'logo') {
                $width = $attrDims[0] ?? null;
                if ($width === null && $sti->exists()) {
                    $dims = @getimagesize($sti->path);
                    if ($dims) $width = $dims[0];
                }
                if ($width && $width > 0) {
                    $repl .= "?w={$width}";
                }
            }
            if ($repl === $imgPath) {
                continue;
            }

            $changes[] = array_merge($match, [
                'original' => $imgPath,
                'reemplazo' => $repl,
            ]);
        }

        if (empty($changes)) {
            return;
        }

        $this->stats['duplicados'] += count($changes);
        $url = $this->comunicadoUrl($comunicado);

        foreach ($changes as $ch) {
            $method = $ch['method'];
            $score = isset($ch['similarity'])
                ? sprintf('%.1f%%', $ch['similarity'])
                : sprintf('d=%d', $ch['distance'] ?? 0);

            $this->line(sprintf(
                '  %s ID %-5d | %-20s -> %-35s | %s (%s) %s',
                $this->dryRun ? '[DRY]' : '[REAL]',
                $comunicado->id,
                basename($ch['original']),
                basename($ch['reemplazo']),
                $score,
                $method,
                isset($firstPageSet[$ch['original']]) ? '[1a pag]' : ''
            ));
            $this->line("           $url");
        }

        if ($this->dryRun) {
            $this->stats['eliminados'] += count($changes);
            return;
        }

        $nuevoTexto = $texto;
        $nuevaImagen = $imagenField;

        foreach ($changes as $ch) {
            $nuevoTexto = $this->replaceMarkdownImage($nuevoTexto, $ch['original'], $ch['reemplazo']);

            if ($nuevaImagen === $ch['original']) {
                $nuevaImagen = $this->normalizeImagenField($ch['reemplazo'], $ch['tipo']);
            }

            $this->deleteDuplicateFile($ch['original']);
        }

        if ($nuevoTexto !== $texto || $nuevaImagen !== $imagenField) {
            \Illuminate\Support\Facades\DB::table('comunicados')
                ->where('id', $comunicado->id)
                ->update([
                    'texto' => $nuevoTexto,
                    'imagen' => $nuevaImagen,
                ]);

            $now = now();
            $morphClass = $comunicado->getMorphClass();

            if ($nuevoTexto !== $texto) {
                DB::table('revisions')->insert([
                    'revisionable_type' => $morphClass,
                    'revisionable_id' => $comunicado->id,
                    'user_id' => null,
                    'key' => 'texto',
                    'old_value' => $texto,
                    'new_value' => $nuevoTexto,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if ($nuevaImagen !== $imagenField) {
                DB::table('revisions')->insert([
                    'revisionable_type' => $morphClass,
                    'revisionable_id' => $comunicado->id,
                    'user_id' => null,
                    'key' => 'imagen',
                    'old_value' => $imagenField,
                    'new_value' => $nuevaImagen,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            $this->stats['cambios']++;
        }
    }

    /**
     * Three-pass matching.
     *
     * @param array{0:int,1:int}|null $attrDims [w,h] from {width=,height=} attributes
     */
    protected function matchImage(string $imagePath, string $logicalPath, array $firstPageSet, ?array $attrDims = null): ?array
    {
        $dhash = ImageHasher::findBestMatch($imagePath, $this->canonicals, $this->dhashThreshold);
        if ($dhash !== null) {
            $this->stats['dhash']++;
            return $dhash;
        }

        $dims = $attrDims ?? ImageHasher::dimensions($imagePath);
        if ($dims === null) {
            return null;
        }

        [$w, $h] = $dims;

        if (isset($firstPageSet[$logicalPath]) && !empty($this->logoCanonicals)) {
            $logo = ImageHasher::findBestColorMatch($imagePath, $this->logoCanonicals, $this->colorThreshold);
            if ($logo !== null) {
                $this->stats['color_logo']++;
                return $logo;
            }
        }

        if (!empty($this->guideCanonicals)) {
            $candidates = [];
            foreach ($this->guideCanonicals as $gc) {
                if (ImageHasher::similarDimensions($w, $h, $gc['width'], $gc['height'])) {
                    $candidates[] = $gc;
                }
            }

            if (!empty($candidates)) {
                $guia = ImageHasher::findBestColorMatch($imagePath, $candidates, $this->guideThreshold);
                if ($guia !== null) {
                    $this->stats['color_guia']++;
                    if ($attrDims !== null) {
                        $this->stats['attrs_usados']++;
                    }
                    return $guia;
                }
            }
        }

        return null;
    }

    protected function firstPageImageSet(string $texto): array
    {
        $lines = explode("\n", $texto);
        $firstPageText = implode("\n", array_slice($lines, 0, 100));
        $set = [];
        foreach (Markdown::images($firstPageText) as $img) {
            $set[$img] = true;
        }
        return $set;
    }

    /**
     * Extract {width=X,height=Y} attributes from markdown images.
     *
     * @return array<string, array{0:int,1:int}>  path => [w, h]
     */
    protected function extractImageAttributes(string $texto): array
    {
        $attrs = [];

        preg_match_all(
            '#!\[[^\]]*\]\(([^)]+)\)\{[^}]*width=(\d+)[^}]*height=(\d+)[^}]*\}#',
            $texto,
            $matches,
            PREG_SET_ORDER
        );

        foreach ($matches as $m) {
            $attrs[$m[1]] = [(int)$m[2], (int)$m[3]];
        }

        return $attrs;
    }

    protected function normalizeImagenField(string $path, string $tipo): string
    {
        if ($tipo === 'guia_con_nombre') {
            $normalized = preg_replace('#/medios/guias/con_nombre/(.*)\.jpg#i', '/medios/guias/$1.jpg', $path);
            return strtolower($normalized);
        }
        return $path;
    }

    protected function replaceMarkdownImage(string $texto, string $oldPath, string $newPath): string
    {
        $escaped = preg_quote($oldPath, '#');
        return preg_replace(
            "#!\[([^\]]*)\]\({$escaped}\)(\{[^}]*\})?#",
            "![$1]($newPath)$2",
            $texto
        );
    }

    protected function deleteDuplicateFile(string $path): void
    {
        try {
            $sti = new StorageItem($path);
            if ($sti->exists()) {
                $sti->delete();
                $this->stats['eliminados']++;
            }
        } catch (\Throwable $e) {
            Log::error("Error eliminando {$path}: {$e->getMessage()}");
            $this->stats['errores']++;
        }
    }

    protected function comunicadoUrl(Comunicado $c): string
    {
        return url('/comunicados/' . ($c->slug ?: $c->id));
    }

    protected function printSummary(): void
    {
        $this->info('=== Resumen ===');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Comunicados revisados', $this->stats['revisados']],
                ['Comunicados modificados', $this->stats['cambios']],
                ['Imágenes duplicadas detectadas', $this->stats['duplicados']],
                ['  - dHash (estructural)', $this->stats['dhash']],
                ['  - Color logo (16x16, 1a pag)', $this->stats['color_logo']],
                ['  - Color guía (dim+16xH)', $this->stats['color_guia']],
                ['Imágenes con {width=,height=}', $this->stats['attrs_total']],
                ['  - usados para pre-filtro dims', $this->stats['attrs_usados']],
                ['Archivos eliminados', $this->stats['eliminados']],
                ['Errores', $this->stats['errores']],
                ['Modo', $this->dryRun ? 'DRY-RUN' : 'CAMBIOS APLICADOS'],
            ]
        );
    }
}
