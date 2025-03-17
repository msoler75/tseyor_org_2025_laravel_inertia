<?php

namespace App\Console\Commands;

define('DEPLOY_FRONT_ENDPOINT', 'https://www.tseyor.xyz/_sendbuild');

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class DeployFront extends Command
{
    protected $signature = 'deploy:front';
    protected $description = 'Comprime los contenidos de la carpeta public/build y los envía por CURL';


    private const SOURCE_DIR = 'public/build';
    private const ZIP_NAME = 'build.zip';

    public function handle()
    {
        try {
            $sourcePath = base_path(self::SOURCE_DIR);
            $zipPath = storage_path('app/' . self::ZIP_NAME);

            Deploy::validateDirectoryExists($sourcePath);

            if (Deploy::createZipFile($sourcePath, $zipPath)) {
                $this->info('ZIP creado: ' . basename($zipPath));

                $result = Deploy::sendZipFile(
                    $zipPath,
                    DEPLOY_FRONT_ENDPOINT,
                    self::ZIP_NAME
                );

                Deploy::handleResponse($result, $this);

                File::delete($zipPath);
            }
            else {
                $this->error('Error al crear el ZIP');
            }

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }




}
