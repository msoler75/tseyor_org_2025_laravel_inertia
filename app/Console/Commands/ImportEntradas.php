<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\EntradaImport;

class ImportEntradas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:entradas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa las entradas en la base de datos desde archivos html';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("$this->description");
        if ($this->confirm('¿Está seguro de que desea importar las entradas? Esto borrará los registros actuales.')) {
            EntradaImport::importar();
            $this->info('¡Las entradas se importaron correctamente!');
        } else {
            $this->info('La importación de entradas fue cancelada.');
        }
    }
}
