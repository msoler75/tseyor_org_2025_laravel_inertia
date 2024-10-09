<?php

namespace App\Console\Commands;

define('ENDPOINT', 'https://www.tseyor.org/_sendbuild');

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use ZipArchive;

class DeployFront extends Command
{
    protected $signature = 'deploy:front';
    protected $description = 'Comprime los contenidos de la carpeta public/build y los envía por CURL';

    private $addedFiles = [];

    public function handle()
    {
        $buildPath = public_path('build');
        $zipPath = storage_path('app' . DIRECTORY_SEPARATOR . 'build.zip');
        // comprobamos si existe el directorio public/build

        if (!File::exists($buildPath)) {
            die("El directorio public/build no existe");
        }

        // Eliminar el archivo zip anterior si existe
        if (File::exists($zipPath)) {
            File::delete($zipPath);
        }

        // Crear el nuevo archivo zip
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $this->addFilesToZip($zip, $buildPath);
            $zip->close();
            $this->info('Archivo zip creado correctamente.');
        } else {
            $this->error('No se pudo crear el archivo zip.');
            return;
        }


        // Enviar el archivo por CURL
        $url = ENDPOINT;

        // Crear un CURLFile con el archivo ZIP
        $zipFile = new \CURLFile($zipPath, 'application/zip', 'build.zip');

        $postFields = [
            'file' => $zipFile
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: multipart/form-data'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($httpCode == 200) {
            $this->info('Archivo enviado correctamente.');
        } else {
            $this->error('Error al enviar el archivo. Código HTTP: ' . $httpCode);
            $this->error('Respuesta: ' . $response);
        }

        curl_close($ch);

        // Eliminar el archivo zip después de enviarlo
        File::delete($zipPath);
    }


    private function addFilesToZip($zip, $path)
    {
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $name => $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($path) + 1);

                // Convertir barras invertidas a barras normales
                $relativePath = str_replace('\\', '/', $relativePath);

                if (!in_array($relativePath, $this->addedFiles)) {
                    $zip->addFile($filePath, $relativePath);
                    $this->addedFiles[] = $relativePath;
                }
            }
        }
    }
}
