<?php

namespace App\Console\Commands;

// Endpoint centralizado en config/deploy.php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class DeployNodeModules extends Command
{
    protected $signature = 'deploy:nodemodules {--package= : Nombre del paquete específico a desplegar (ej: @tiptap)}';
    protected $description = 'Comprime node_modules o un paquete específico para que funcione SSR y lo envía al servidor';

    private const SOURCE_DIR = 'node_modules';
    private const ZIP_NAME = 'nodemodules.zip';

    // Exclusiones definidas en config/deploy.php
    // Se leen en tiempo de ejecución para mantener un único origen de verdad

    public function handle()
    {
        try {
            $package = $this->option('package');

            if ($package && !File::exists(base_path('node_modules/' . $package))) {
                $this->error("El paquete '{$package}' no existe en node_modules.");
                return;
            }

            $sourceDir = $package ? 'node_modules/' . $package : 'node_modules';
            $zipName = $package ? str_replace('@', 'at', $package) . '.zip' : 'nodemodules.zip';
            $baseName = $package ?: 'node_modules';

            $sourcePath = base_path($sourceDir);
            $zipPath = storage_path('app/' . $zipName);

            Deploy::validateDirectoryExists($sourcePath);

            $this->info('Creando zip...');

            if (Deploy::createZipFile(
                $sourcePath,
                $zipPath,
                $package ? [] : config('deploy.node_modules_exclusions', []),
                $baseName
            )) {
                if (!File::exists($zipPath)) {
                    $this->error('Error: El ZIP se reportó como creado pero no se encuentra en ' . $zipPath);
                    return;
                }
                $this->info('ZIP creado: ' . basename($zipPath));

                $this->info('Enviando...');
                $endpoint = config('deploy.node_modules_endpoint');
                if ($package) {
                    $endpoint .= '?type=package';
                }
                $result = Deploy::sendZipFile(
                    $zipPath,
                    $endpoint,
                    $zipName,
                );

                Deploy::handleResponse($result, $this);

                // File::delete($zipPath);
            } else {
                $this->error('Error al crear el ZIP');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
