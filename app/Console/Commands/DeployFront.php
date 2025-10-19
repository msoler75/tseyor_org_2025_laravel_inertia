<?php

namespace App\Console\Commands;

// Endpoint centralizado en config/deploy.php

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class DeployFront extends Command
{
    protected $signature = 'deploy:front {--rollback : Realizar rollback a la versión anterior}';
    protected $description = 'Comprime los contenidos de la carpeta public/build y los envía por CURL, o realiza rollback si se especifica --rollback';


    private const SOURCE_DIR = 'public/build';
    private const ZIP_NAME = 'build.zip';

    public function handle()
    {
        try {
            if ($this->option('rollback')) {
                $this->info('Realizando rollback del frontend...');
                $result = Deploy::sendRollbackRequest(config('deploy.rollback_endpoint'));
                Deploy::handleResponse($result, $this);
            } else {
                $sourcePath = base_path(self::SOURCE_DIR);
                $zipPath = storage_path('app/' . self::ZIP_NAME);

                Deploy::validateDirectoryExists($sourcePath);

                if (Deploy::createZipFile($sourcePath, $zipPath)) {
                    $this->info('ZIP creado: ' . basename($zipPath));

                    $result = Deploy::sendZipFile(
                        $zipPath,
                        config('deploy.front_endpoint'),
                        self::ZIP_NAME
                    );

                    Deploy::handleResponse($result, $this);

                    // File::delete($zipPath);
                }
                else {
                    $this->error('Error al crear el ZIP');
                }
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }




}
