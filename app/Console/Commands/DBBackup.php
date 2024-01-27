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
        $carpeta = "storage/backups";

        echo "Creando copia...\n";

        $databaseName = DB::getDatabaseName();

        // Obtiene todos los nombres de las tablas de la base de datos
        $config = DB::getConfig();
        $backupFileName = $carpeta . "/" . $databaseName . '_backup_' . Carbon::now()->timestamp . '.sql';

        $url = "mysql:host={$config['host']}:{$config['port']};dbname={$config['database']}";
        //dd($config);
        // dd($url);
        try {
            $dump = new Mysqldump\Mysqldump($url, $config['username'], $config['password']);
            $dump->start( $backupFileName);
            echo "Backup creado correctamente en el archivo: $backupFileName";
        } catch (\Exception $e) {
            echo "Error al crear el backup: " . $e->getMessage();
        }

    }
}
