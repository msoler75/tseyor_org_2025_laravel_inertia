<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Druidfi\Mysqldump;

class DBRestore extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restaura la base de datos desde una copia seguridad';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("$this->description");

        if ($this->confirm('¿Está seguro de que desea importar la copia? Esto borrará todos los datos de la base de datos.')) {
            echo "Restaurando...\n";

            die('NO IMPLEMENTADO');

            // donde se guardarán los backups
            $carpeta = "storage/backups";


            $databaseName = DB::getDatabaseName();

            // ComunicadoImport::importar();
            $this->info('¡La base de datos se restauró correctamente!');
        } else {
            $this->info('La restauración fue cancelada.');
        }


    }
}
