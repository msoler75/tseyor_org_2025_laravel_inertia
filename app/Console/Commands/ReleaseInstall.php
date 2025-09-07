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
            $this->info('Instalación de node_modules, public/build y ssr.js desde storage/install...');

            $results = Deploy::installAllFromStorageInstall();

            $installed = [];
            $skipped = [];
            $failed = [];

            foreach ($results as $key => $data) {
                $status = strtolower($data['status'] ?? 'unknown');
                $file = $data['file'] ?? null;
                $message = $data['message'] ?? '';

                $label = $key . ($file ? " (file: {$file})" : '');

                if (in_array($status, ['installed', 'ok', 'success'])) {
                    $installed[] = $label;
                } elseif (in_array($status, ['skipped', 'skip'])) {
                    // Normalizar mensajes redundantes (por ejemplo provenientes de DeployHelper)
                    $normalizedMessage = '';
                    if (!empty($message)) {
                        $lowMsg = strtolower($message);
                        if (strpos($lowMsg, 'missing file') !== false || strpos($lowMsg, 'all installations skipped') !== false) {
                            $normalizedMessage = 'ZIP no encontrado';
                        } elseif (strpos($lowMsg, 'present but skipped') !== false || strpos($lowMsg, 'present but omitted') !== false) {
                            $normalizedMessage = 'ZIP caducado (no apto)';
                        } else {
                            $normalizedMessage = $message;
                        }
                    }

                    if (empty($file) && empty($normalizedMessage)) {
                        $skipped[] = $key . ' — ZIP no encontrado';
                    } else {
                        $skipped[] = $label . ($normalizedMessage ? ' — ' . $normalizedMessage : '');
                    }
                } else {
                    $failed[] = $label . ($message ? ' — ' . $message : '');
                }
            }

            /*if (count($installed) > 0) {
                $this->info('Instalado: ' . implode(', ', $installed));
            }*/

            if (count($skipped) > 0) {
                $this->line('Omitido: ' . implode(', ', $skipped));
            }

            if (count($failed) > 0) {
                $this->error('Falló: ' . implode(', ', $failed));
            }

            if (count($failed) === 0 && count($installed) > 0) {
                $this->info('Instalación completada correctamente.');
                // marcar tarea 1 completa
                return 0;
            }

            $this->error('Instalación incompleta o fallida.');
            return 1;
        } catch (Exception $e) {
            $this->error('Error ejecutando instalación: ' . $e->getMessage());
            return 1;
        }
    }
}
