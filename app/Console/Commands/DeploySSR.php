<?php

namespace App\Console\Commands;

// Endpoints centralizados en config/deploy.php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class DeploySSR extends Command
{
    protected $signature = 'deploy:ssr {--session-token= : Token temporal del dashboard admin para bypass de IP}';
    protected $description = 'Comprime los contenidos de la carpeta bootstrap/ssr y los envía por CURL';


    private const SOURCE_DIR = 'bootstrap/ssr';
    private const ZIP_NAME = 'ssr.zip';



    public function handle()
    {
        try {
            $sourcePath = base_path(self::SOURCE_DIR);
            $zipPath = storage_path('app/' . self::ZIP_NAME);

            Deploy::validateDirectoryExists($sourcePath);

            // Excluir el manifest PWA ya que no es necesario para SSR
            $exclusions = ['tseyor-manifest.v2.json'];

            if (Deploy::createZipFile($sourcePath, $zipPath, $exclusions)) {
                $this->info('ZIP creado: ' . basename($zipPath));

                $extraHeaders = [];
                $sessionToken = $this->option('session-token');
                if ($sessionToken) {
                    $extraHeaders[] = 'X-Deploy-Session-Token: ' . $sessionToken;
                }

                $result = Deploy::sendZipFile(
                    $zipPath,
                    config('deploy.ssr_endpoint'),
                    self::ZIP_NAME,
                    $extraHeaders,
                );

                Deploy::handleResponse($result, $this);

                // File::delete($zipPath);
            }
            else {
                $this->error('Error al crear el ZIP');
            }

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }






}
