<?php

namespace App\Console\Commands;

use App\Models\Libro;
use Illuminate\Console\Command;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Pigmalion\StorageItem;

class GenerarLqipLibros extends Command
{
    protected $signature = 'libros:generar-lqip {--force : Resobreescribir LQIPs existentes}';
    protected $description = 'Genera imágenes LQIP (base64) para todos los libros y las guarda en BD';

    public function handle(): int
    {
        $libros = Libro::whereNotNull('imagen')->get();

        if ($libros->isEmpty()) {
            $this->info('No hay libros con imagen.');
            return 0;
        }

        $manager = new ImageManager(new Driver());
        $count = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($libros as $libro) {
            if ($libro->imagen_lqip && !$this->option('force')) {
                $skipped++;
                continue;
            }

            try {
                $libro->generarLqip();
                $libro->timestamps = false;
                $libro->saveQuietly();

                $size = round(strlen(str_replace('data:image/jpeg;base64,', '', $libro->imagen_lqip ?? '')) / 1024, 1);
                $this->line(" <info>✓</info> libro #{$libro->id}: {$size}KB — {$libro->titulo}");
                $count++;
            } catch (\Throwable $e) {
                $this->error(" {$libro->id}: error — {$e->getMessage()}");
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Generados: $count | Saltados: $skipped | Fallos: $failed");

        return 0;
    }
}
