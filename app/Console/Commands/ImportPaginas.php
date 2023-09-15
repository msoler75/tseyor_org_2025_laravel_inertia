<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\PaginaImport;

class ImportPaginas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:paginas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa las páginas en la base de datos desde archivo json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo $this->description . "\n";
        if ($this->confirm('¿Está seguro de que desea importar las páginas? Esto borrará los registros actuales.')) {
            PaginaImport::importar();
            $this->info('¡Las páginas se importaron correctamente!');
        } else {
            $this->info('La importación de páginas fue cancelada.');
        }
    }
}
