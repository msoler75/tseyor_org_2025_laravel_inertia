<?php

namespace App\Services;

use App\Pigmalion\ImageHasher;
use App\Pigmalion\Markdown;
use App\Pigmalion\StorageItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * Deduplica imágenes en el texto e imagen de portada de un comunicado:
 * reemplaza copias locales del sello Tseyor y guías estelares por
 * referencias a las versiones canónicas en /almacen/medios/logos/ y
 * /almacen/medios/guias/.
 *
 * Uso típico desde el modelo:
 *   ImageDeduplicationService::deduplicate($comunicado);
 *
 * La instancia cachea las imágenes canónicas (singleton).
 */
class ImageDeduplicationService
{
    /** @var array|null Canonicals with precomputed pixels */
    private static ?array $canonicals = null;

    /** @var array|null Logo canonicals at 16x16 */
    private static ?array $logoCanonicals = null;

    /** @var array|null Guide canonicals at 16xH */
    private static ?array $guideCanonicals = null;

    /**
     * Deduplicate images in a comunicado's texto and imagen fields.
     * Modifies the model attributes IN PLACE. Does NOT persist.
     *
     * @return array{texto: string, imagen: string, changes: int}
     */
    public static function deduplicate(Model $comunicado): array
    {
        self::loadCanonicals();

        $texto = $comunicado->texto ?? '';
        $imagenField = $comunicado->imagen ?? '';

        $allPaths = array_unique(array_merge(
            Markdown::images($texto),
            ! empty($imagenField) ? [$imagenField] : []
        ));

        $localPaths = [];
        foreach ($allPaths as $p) {
            if (strpos($p, '/almacen/medios/comunicados/') !== false) {
                $localPaths[] = $p;
            }
        }

        if (empty($localPaths)) {
            return ['texto' => $texto, 'imagen' => $imagenField, 'changes' => 0];
        }

        $firstPageSet = self::firstPageImageSet($texto);
        $imageAttrs = self::extractImageAttributes($texto);
        $changes = [];

        foreach ($localPaths as $imgPath) {
            $attrDims = $imageAttrs[$imgPath] ?? null;
            $sti = new StorageItem($imgPath);

            if (! $sti->exists()) {
                continue;
            }

            $match = self::matchImage($sti->path, $imgPath, $firstPageSet, $attrDims);

            if ($match === null) {
                continue;
            }

            $repl = $match['path'];

            // Para logos: siempre usar el sello transparente grande mostrado a 64x64.
            if ($match['tipo'] === 'logo') {
                $repl = '/almacen/medios/logos/SELLO_TRANSPARENTE_GRANDE.png';
            }

            // Para guias: la dimensión de visualización se mantiene con el sufijo
            // {width=...,height=...} (formato propio de la app), que se conserva al
            // reemplazar la ruta. Si la imagen original no lo tiene, se añade con
            // ancho fijo 220 (altura proporcional) para que la canónica no se muestre enorme.
            $addDims = null;
            if ($match['tipo'] === 'logo') {
                $addDims = [64, 64];
            } elseif ($attrDims === null) {
                $dims = @getimagesize($sti->path);
                if ($dims && $dims[0] > 0 && $dims[1] > 0) {
                    $targetW = 220;
                    $targetH = (int) round($dims[1] * ($targetW / $dims[0]));
                    $addDims = [$targetW, $targetH];
                }
            }

            if ($repl === $imgPath) {
                continue;
            }

            $changes[] = [
                'original' => $imgPath,
                'reemplazo' => $repl,
                'tipo' => $match['tipo'],
                'method' => $match['method'] ?? '?',
                'distance' => $match['distance'] ?? null,
                'similarity' => $match['similarity'] ?? null,
                'add_dims' => $addDims,
            ];
        }

        if (empty($changes)) {
            return ['texto' => $texto, 'imagen' => $imagenField, 'changes' => 0];
        }

        $nuevoTexto = $texto;
        $nuevaImagen = $imagenField;

        foreach ($changes as $ch) {
            $nuevoTexto = self::replaceMarkdownImage($nuevoTexto, $ch['original'], $ch['reemplazo']);

            // Para guias sin {width=,height=} previo: fijar dimensiones con el sufijo propio
            if ($ch['add_dims'] !== null) {
                $nuevoTexto = self::ensureImageDims($nuevoTexto, $ch['reemplazo'], $ch['add_dims']);
            }

            if ($nuevaImagen === $ch['original']) {
                $nuevaImagen = self::normalizeImagenField($ch['reemplazo'], $ch['tipo']);
            }

            self::deleteFile($ch['original']);
        }

        $comunicado->texto = $nuevoTexto;
        $comunicado->imagen = $nuevaImagen;

        Log::info("ImageDeduplicationService: comunicado #{$comunicado->id} — ".count($changes).' duplicados reemplazados');

        return ['texto' => $nuevoTexto, 'imagen' => $nuevaImagen, 'changes' => count($changes)];
    }

    /**
     * Lazily load and cache canonical images.
     */
    private static function loadCanonicals(): void
    {
        if (self::$canonicals !== null) {
            return;
        }

        $logoDir = new StorageItem('/almacen/medios/logos');
        $guiasDir = new StorageItem('/almacen/medios/guias');
        $guiasConNombreDir = new StorageItem('/almacen/medios/guias/con_nombre');

        $logos = [];
        $guias = [];

        if ($logoDir->directoryExists()) {
            foreach ($logoDir->files() as $fp) {
                $fn = basename($fp);
                if (preg_match('/^sello/i', $fn) && ! preg_match('/\.svg$/i', $fn)) {
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

        self::$logoCanonicals = ImageHasher::precomputeCanonicals($logos, 16, 16);
        self::$guideCanonicals = ImageHasher::precomputeCanonicals($guias, 16, null);
        self::$canonicals = array_merge(self::$logoCanonicals, self::$guideCanonicals);
    }

    private static function matchImage(string $imagePath, string $logicalPath, array $firstPageSet, ?array $attrDims = null): ?array
    {
        $dhash = ImageHasher::findBestMatch($imagePath, self::$canonicals, 3);
        if ($dhash !== null) {
            return $dhash;
        }

        $dims = $attrDims ?? ImageHasher::dimensions($imagePath);
        if ($dims === null) {
            return null;
        }

        [$w, $h] = $dims;

        if (isset($firstPageSet[$logicalPath]) && ! empty(self::$logoCanonicals)) {
            $logo = ImageHasher::findBestColorMatch($imagePath, self::$logoCanonicals, 90);
            if ($logo !== null) {
                return $logo;
            }
        }

        if (! empty(self::$guideCanonicals)) {
            $candidates = [];
            foreach (self::$guideCanonicals as $gc) {
                if (ImageHasher::similarDimensions($w, $h, $gc['width'], $gc['height'])) {
                    $candidates[] = $gc;
                }
            }

            if (! empty($candidates)) {
                return ImageHasher::findBestColorMatch($imagePath, $candidates, 95);
            }
        }

        return null;
    }

    private static function firstPageImageSet(string $texto): array
    {
        $lines = explode("\n", $texto);
        $firstPageText = implode("\n", array_slice($lines, 0, 100));
        $set = [];
        foreach (Markdown::images($firstPageText) as $img) {
            $set[$img] = true;
        }

        return $set;
    }

    private static function extractImageAttributes(string $texto): array
    {
        $attrs = [];
        preg_match_all(
            '#!\[[^\]]*\]\(([^)]+)\)\{[^}]*width=(\d+)[^}]*height=(\d+)[^}]*\}#',
            $texto,
            $matches,
            PREG_SET_ORDER
        );
        foreach ($matches as $m) {
            $attrs[$m[1]] = [(int) $m[2], (int) $m[3]];
        }

        return $attrs;
    }

    private static function normalizeImagenField(string $path, string $tipo): string
    {
        if ($tipo === 'guia_con_nombre') {
            $normalized = preg_replace('#/medios/guias/con_nombre/(.*)\.jpg#i', '/medios/guias/$1.jpg', $path);

            return strtolower($normalized);
        }

        return $path;
    }

    private static function replaceMarkdownImage(string $texto, string $oldPath, string $newPath): string
    {
        $escaped = preg_quote($oldPath, '#');

        return preg_replace(
            "#!\[([^\]]*)\]\({$escaped}\)(\{[^}]*\})?#",
            "![$1]($newPath)$2",
            $texto
        );
    }

    /**
     * Añade el sufijo {width=W,height=H} después de la imagen si no lo tiene ya.
     * Se usa para fijar las dimensiones de una guía canónica sin inyectar ?w= en la URL.
     */
    private static function ensureImageDims(string $texto, string $path, array $dims): string
    {
        [$w, $h] = $dims;
        $escaped = preg_quote($path, '#');

        return preg_replace(
            "#!\[([^\]]*)\]\({$escaped}\)(\{[^}]*\})?#",
            "![$1]($path){width=$w,height=$h}",
            $texto
        );
    }

    private static function deleteFile(string $path): void
    {
        try {
            $sti = new StorageItem($path);
            if ($sti->exists()) {
                $sti->delete();
            }
        } catch (\Throwable $e) {
            Log::error("ImageDeduplicationService: error eliminando {$path}: {$e->getMessage()}");
        }
    }
}
