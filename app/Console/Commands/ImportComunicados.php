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
    protected $description = 'Importa los comunicados en la base de datos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        ComunicadoImport::importar();
    }
}
