<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Pigmalion\DeployHelper as Deploy;
use Exception;

class ReleaseInstall extends Command
{
    protected $signature = 'release:install';
    protected $description = 'Instala en el servidor los zips preparados en storage/install (public_build, ssr, node_modules)';

    public function handle()
    {
        try {
            $this->info('Ejecutando instalación desde storage/install...');

            $results = Deploy::installAllFromStorageInstall();

            foreach ($results as $key => $data) {
                $status = $data['status'] ?? 'unknown';
                $file = $data['file'] ?? null;
                $message = $data['message'] ?? '';

                $this->line("- {$key}: {$status} " . ($file ? "(file: {$file})" : '') . " {$message}");
            }

            $this->info('Proceso finalizado.');
            return 0;
        } catch (Exception $e) {
            $this->error('Error ejecutando instalación: ' . $e->getMessage());
            return 1;
        }
    }
}
