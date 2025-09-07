<?php

namespace App\Console\Commands;

// Endpoints centralizados en config/deploy.php
// Se usan mediante config('deploy.*')

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class ReleasePrepare extends Command
{
    protected $signature = 'release:prepare';
    protected $description = 'Crea los zips de nodemodules, front (public/build) y ssr.js y los envía al servidor para una nueva release';

    public function handle()
    {
    // Imprimir descripción del comando
    $this->info('Descripción: ' . $this->description);

    // Confirmación previa a la ejecución: verificar que se corrió npm run build-all
        if (!$this->confirm('¿Has ejecutado "npm run build-all" previamente?')) {
            $this->info('Operación cancelada. Ejecuta "npm run build-all" antes de continuar.');
            return 0;
        }

        $jobs = [
            [
                'name' => 'nodemodules',
                'source' => base_path('node_modules'),
                'zip' => storage_path('app/nodemodules.zip'),
                'endpoint' => config('deploy.node_modules_endpoint'),
                'zip_name' => 'nodemodules.zip',
                'options' => [
                    'exclusions' => config('deploy.node_modules_exclusions', []),
                    'basePrefix' => 'node_modules'
                ]
            ],
            [
                'name' => 'front',
                'source' => base_path('public/build'),
                'zip' => storage_path('app/build.zip'),
                'endpoint' => config('deploy.front_endpoint'),
                'zip_name' => 'build.zip',
                'options' => []
            ],
            [
                'name' => 'ssr',
                'source' => base_path('bootstrap/ssr'),
                'zip' => storage_path('app/ssr.zip'),
                'endpoint' => config('deploy.ssr_endpoint'),
                'zip_name' => 'ssr.zip',
                'options' => []
            ]
        ];

        foreach ($jobs as $job) {
            try {
                $this->info("Preparando: {$job['name']}");

                Deploy::validateDirectoryExists($job['source']);

                $exclusions = $job['options']['exclusions'] ?? [];
                $basePrefix = $job['options']['basePrefix'] ?? '';

                if (Deploy::createZipFile($job['source'], $job['zip'], $exclusions, $basePrefix)) {
                    $this->info('ZIP creado: ' . basename($job['zip']));

                    $this->info('Enviando con flag prepare...');
                    $result = Deploy::sendZipFile(
                        $job['zip'],
                        $job['endpoint'],
                        $job['zip_name'],
                        [],
                        ['prepare' => '1']
                    );

                    Deploy::handleResponse($result, $this);

                    // eliminar zip temporal
                    if (File::exists($job['zip'])) {
                        File::delete($job['zip']);
                    }
                } else {
                    $this->error("Error al crear ZIP para {$job['name']}");
                }
            } catch (Exception $e) {
                $this->error("Error en {$job['name']}: " . $e->getMessage());
            }
        }

        $this->info('Release prepare completado.');
    }
}
