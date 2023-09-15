<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\ComunicadoImport;

class ImportComunicados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:comunicados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los comunicados en la base de datos desde documentos Word y listado de comunicados .lst';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo $this->description . "\n";
        if ($this->confirm('¿Está seguro de que desea importar los comunicados? Esto borrará los registros actuales.')) {
            ComunicadoImport::importar();
            $this->info('¡Los comunicados se importaron correctamente!');
        } else {
            $this->info('La importación de comunicados fue cancelada.');
        }

    }
}
