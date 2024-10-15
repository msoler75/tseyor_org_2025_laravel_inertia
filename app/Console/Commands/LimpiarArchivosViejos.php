<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\StorageItem;

class LimpiarArchivosViejos extends Command
{
    protected $signature = 'limpiar:archivos {carpeta=archivos}';
    protected $description = 'Elimina archivos .files e index.html en la carpeta especificada y sus subcarpetas';

    public function handle()
    {
        $carpeta = $this->argument('carpeta');

        // si $carpeta no empieza por /archivos o archivos entonces da error
        if (!preg_match("#\/?archivos\/?#", $carpeta)) {
            $this->error("Ha de ser una carpeta de archivos.");
            return;
        }

        $loc = new StorageItem($carpeta);

        $realPath = $loc->path;

        if (!File::isDirectory($realPath)) {
            $this->error("La carpeta especificada no existe.");
            return;
        }

        $this->eliminarArchivos($realPath);
        $this->info("\nProceso completado.");
    }

    private function eliminarArchivos($carpeta)
{
    $dir = StorageItem::fromPath($carpeta)->location;

    // Limpiar la línea actual y mover el cursor al inicio
    $this->output->write("\033[2K\r");
    $this->output->write("Explorando: " . $dir);

    $archivos = scandir($carpeta);
    $eliminados = 0;
    $omitidos = 0;

    foreach ($archivos as $archivo) {
        if ($archivo === '.' || $archivo === '..') {
            continue;
        }

        $filePath = $carpeta . DIRECTORY_SEPARATOR . $archivo;
        if (!is_file($filePath)) {
            continue;
        }

        $sti = StorageItem::fromPath($filePath);

        if ($archivo === 'index.html' || $archivo === '.files') {
            $eliminar = ($archivo === '.files');

            if ($archivo === 'index.html') {
                // Mover a una nueva línea para la pregunta
                $this->output->write("\n");
                if ($this->confirm("¿Desea eliminar el archivo index.html en {$sti->location}?")) {
                    $eliminar = true;
                }
            }

            if (!$eliminar) {
                $omitidos++;
            } elseif (unlink($filePath)) {
                $eliminados++;
            }
        }
    }

    // Actualizar la línea con el resumen
    $this->output->write("\033[2K\r");
    $this->output->write("Procesado: $dir (Eliminados: $eliminados, Omitidos: $omitidos)");

    $subcarpetas = array_filter(scandir($carpeta), function($item) use ($carpeta) {
        return is_dir($carpeta . DIRECTORY_SEPARATOR . $item) && $item !== '.' && $item !== '..';
    });

    if (!empty($subcarpetas)) {
        $this->output->write("\n");
    }

    foreach ($subcarpetas as $subcarpeta) {
        $this->eliminarArchivos($carpeta . DIRECTORY_SEPARATOR . $subcarpeta);
    }
}
}
