<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Druidfi\Mysqldump;

class DBBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una copia de seguridad de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("$this->description");

        // donde se guardarán los backups
        $carpeta = storage_path() ."/backups";

        $this->info("Creando copia...");

        $databaseName = DB::getDatabaseName();

        // Obtiene todos los nombres de las tablas de la base de datos
        $config = DB::getConfig();
        $backupFileName = $carpeta . "/" . $databaseName . '_backup_' . Carbon::now()->timestamp . '.sql';
        $zipFileName  = $backupFileName . ".zip";

        $url = "mysql:host={$config['host']}:{$config['port']};dbname={$config['database']}";

        try {
            $dump = new Mysqldump\Mysqldump($url, $config['username'], $config['password']);
            $dump->start($backupFileName);

            // comprimir en zip:
            $this->info("Comprimiendo...");

            $zip = new \ZipArchive();
            if ($zip->open($zipFileName, \ZipArchive::CREATE) !== true) {
                $this->info("Backup creado correctamente en el archivo: $backupFileName");
                $this->error("Error al crear el archivo ZIP.");
                exit;
            }

            // agregar el archivo
            $zip->addFile($backupFileName, basename($backupFileName));

            // Cerrar el archivo ZIP
            $zip->close();

            // echo "Borramos $backupFileName\n";
            unlink($backupFileName);

            $this->info("Backup creado correctamente en el archivo: $zipFileName");
        } catch (\Exception $e) {
            $this->error("Error al crear el backup: " . $e->getMessage());
        }
    }
}
