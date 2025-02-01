<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\PsicografiaImport;

class ImportPsicografias extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:psicografias';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa las psicografias desde la carpeta /almacen/medios/psicografias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("$this->description");
        if ($this->confirm('¿Está seguro de que desea importar las psicografías? Esto borrará los registros actuales.')) {
            PsicografiaImport::importar();
            $this->info('¡Las psicografías se importaron correctamente!');
        } else {
            $this->info('La importación de psicografías fue cancelada.');
        }
    }
}
