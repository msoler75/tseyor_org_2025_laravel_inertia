<?php

namespace App\Console\Commands;

// Endpoint centralizado en config/deploy.php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class DeployNodeModules extends Command
{
    protected $signature = 'deploy:nodemodules';
    protected $description = 'Comprime node_modules para que funcione SSR y lo envía al servidor';

    private const SOURCE_DIR = 'node_modules';
    private const ZIP_NAME = 'nodemodules.zip';

    // Exclusiones definidas en config/deploy.php
    // Se leen en tiempo de ejecución para mantener un único origen de verdad

    public function handle()
    {
        try {
            $sourcePath = base_path(self::SOURCE_DIR);
            $zipPath = storage_path('app/' . self::ZIP_NAME);

            Deploy::validateDirectoryExists($sourcePath);

            $this->info('Creando zip...');

            if (Deploy::createZipFile(
                $sourcePath,
                $zipPath,
                config('deploy.node_modules_exclusions', []),
                'node_modules'
            )) {
                $this->info('ZIP creado: ' . basename($zipPath));

                $this->info('Enviando...');
                $result = Deploy::sendZipFile(
                    $zipPath,
                    config('deploy.node_modules_endpoint'),
                    self::ZIP_NAME,
                );

                Deploy::handleResponse($result, $this);

                File::delete($zipPath);
            } else {
                $this->error('Error al crear el ZIP');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
