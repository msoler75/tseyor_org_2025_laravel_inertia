<?php

namespace App\Console\Commands;

use App\Models\Galeria;
use App\Models\GaleriaItem;
use App\Models\Nodo;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ImportLegacyDibujosGaleria extends Command
{
    protected $signature = 'import:galeria-dibujos
        {file=e:/2021.json : Ruta al JSON legado}
        {--titulo=Galeria dibujos importada 2021 : Titulo de la nueva galeria}
        {--descripcion=Importacion desde JSON legado (solo dibujos) : Descripcion de la galeria}
        {--ruta=dibujo-y-pintura : Ruta base de la galeria}';

    protected $description = 'Importa solo la seccion Dibujos de un JSON legado a Galeria y GaleriaItem';

    public function handle(): int
    {
        $file = (string) $this->argument('file');

        if (!is_file($file) || !is_readable($file)) {
            $this->error("No se puede leer el archivo JSON: {$file}");
            return self::FAILURE;
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            $this->error("No se pudo cargar el archivo JSON: {$file}");
            return self::FAILURE;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $this->error('El archivo no contiene un JSON valido.');
            return self::FAILURE;
        }

        $dibujos = $data['Dibujos'] ?? null;
        if (!is_array($dibujos)) {
            $this->error('No se encontro la seccion Dibujos en el JSON.');
            return self::FAILURE;
        }

        if (count($dibujos) === 0) {
            $this->warn('La seccion Dibujos esta vacia. No hay nada que importar.');
            return self::SUCCESS;
        }

        $galeria = Galeria::create([
            'titulo' => (string) $this->option('titulo'),
            'descripcion' => (string) $this->option('descripcion'),
            'ruta' => (string) $this->option('ruta'),
            'imagen' => null,
        ]);

        $userMap = $this->buildUserMap();
        $orden = 1;
        $importados = 0;
        $sinUsuario = 0;
        $saltados = 0;
        $primeraImagen = null;

        foreach ($dibujos as $index => $item) {
            if (!is_array($item)) {
                $saltados++;
                continue;
            }

            $url = trim((string) ($item['url'] ?? ''));
            if ($url === '') {
                $this->warn('Item sin url en indice ' . $index . '. Se omite.');
                $saltados++;
                continue;
            }

            $titulo = trim((string) ($item['titulo'] ?? ''));
            if ($titulo === '') {
                $titulo = pathinfo($url, PATHINFO_FILENAME);
            }

            $autor = trim((string) ($item['autor'] ?? ''));
            $descripcion = trim((string) ($item['descripcion'] ?? ''));

            $userId = $this->resolveUserId($autor, $userMap);
            if ($userId === null && $autor !== '') {
                $sinUsuario++;
                $descripcion = $this->appendAutorToDescripcion($descripcion, $autor);
            }

            $nodo = Nodo::firstOrCreate(
                ['ubicacion' => $url],
                [
                    'permisos' => '0644',
                    'user_id' => 1,
                    'group_id' => 1,
                    'es_carpeta' => 0,
                    'oculto' => 0,
                ]
            );

            GaleriaItem::create([
                'galeria_id' => $galeria->id,
                'nodo_id' => $nodo->id,
                'titulo' => $titulo,
                'descripcion' => $descripcion !== '' ? $descripcion : null,
                'user_id' => $userId,
                'orden' => $orden++,
            ]);

            if ($primeraImagen === null) {
                $primeraImagen = $nodo->ubicacion;
            }

            $importados++;
        }

        if ($primeraImagen !== null) {
            $galeria->imagen = $primeraImagen;
            $galeria->save();
        }

        $this->info('Importacion completada.');
        $this->line('Galeria creada con ID: ' . $galeria->id);
        $this->line('Items importados: ' . $importados);
        $this->line('Items sin usuario asignado: ' . $sinUsuario);
        $this->line('Items omitidos: ' . $saltados);
        $this->line('Seccion Fotografia: ignorada.');

        return self::SUCCESS;
    }

    /**
     * @return array<string,int>
     */
    private function buildUserMap(): array
    {
        $map = [];

        User::select('id', 'name', 'slug')
            ->get()
            ->each(function (User $user) use (&$map): void {
                $nameKey = $this->normalizeAuthorKey((string) $user->name);
                if ($nameKey !== '' && !isset($map[$nameKey])) {
                    $map[$nameKey] = $user->id;
                }

                $slug = (string) ($user->slug ?? '');
                if ($slug !== '') {
                    $slugKey = $this->normalizeAuthorKey($slug);
                    if ($slugKey !== '' && !isset($map[$slugKey])) {
                        $map[$slugKey] = $user->id;
                    }
                }
            });

        return $map;
    }

    private function resolveUserId(string $autor, array $userMap): ?int
    {
        if ($autor === '') {
            return null;
        }

        $key = $this->normalizeAuthorKey($autor);
        if ($key === '') {
            return null;
        }

        return $userMap[$key] ?? null;
    }

    private function normalizeAuthorKey(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->ascii()
            ->replaceMatches('/[^a-z0-9]+/', '')
            ->toString();
    }

    private function appendAutorToDescripcion(string $descripcion, string $autor): string
    {
        $lineaAutor = 'Autor: ' . $autor;

        if ($descripcion === '') {
            return $lineaAutor;
        }

        if (Str::contains($descripcion, $lineaAutor)) {
            return $descripcion;
        }

        return $descripcion . PHP_EOL . PHP_EOL . $lineaAutor;
    }
}
