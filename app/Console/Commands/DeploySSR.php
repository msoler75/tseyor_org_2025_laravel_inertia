<?php

namespace App\Console\Commands;

define('APP_HOST', 'www.tseyor.xyz');

define('DEPLOY_SSR_ENDPOINT', 'https://'. APP_HOST .'/_sendssr');

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class DeploySSR extends Command
{
    protected $signature = 'deploy:ssr';
    protected $description = 'Comprime los contenidos de la carpeta bootstrap/ssr y los envía por CURL';


    private const SOURCE_DIR = 'bootstrap/ssr';
    private const ZIP_NAME = 'ssr.zip';



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
                    DEPLOY_SSR_ENDPOINT,
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
