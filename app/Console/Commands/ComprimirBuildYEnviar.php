<?php

namespace App\Console\Commands;


define('ENDPOINT', 'https://tseyor.xyz/_sendbuild');

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

class ComprimirBuildYEnviar extends Command
{
    protected $signature = 'build:send';
    protected $description = 'Comprime la carpeta public/build y la envía por CURL';

    public function handle()
    {
        $buildPath = public_path('build');
        $zipPath = storage_path('app/build.zip');

        // Eliminar el archivo zip anterior si existe
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        // Crear el nuevo archivo zip
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = File::allFiles($buildPath);

            foreach ($files as $file) {
                $zip->addFile($file->getRealPath(), $file->getRelativePathname());
            }

            $zip->close();
            $this->info('Archivo zip creado correctamente.');
        } else {
            $this->error('No se pudo crear el archivo zip.');
            return;
        }

        // Enviar el archivo por CURL
        $url = ENDPOINT; // Reemplaza con tu URL real
        $curlFile = curl_file_create($zipPath, 'application/zip', 'build.zip');

        $postFields = [
            'file' => $curlFile
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            $this->info('Archivo enviado correctamente.');
        } else {
            $this->error('Error al enviar el archivo. Código HTTP: ' . $httpCode);
        }

        curl_close($ch);

        // Eliminar el archivo zip después de enviarlo
        File::delete($zipPath);
    }
}
