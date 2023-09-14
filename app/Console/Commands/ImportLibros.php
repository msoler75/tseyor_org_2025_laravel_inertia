<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\LibroImport;

class ImportLibros extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:libros';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los libros en la base de datos desde archivos .html de la web anterior (/biblioteca/libros/*.html)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        LibroImport::importar();

        LibroImport::fusionarCategoriasSimilares();
    }
}
