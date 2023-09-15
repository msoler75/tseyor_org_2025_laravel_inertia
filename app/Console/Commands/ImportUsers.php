<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\UserImport;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los usuarios desde archivo json';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo $this->description . "\n";
        if ($this->confirm('¿Está seguro de que desea importar los usuarios? Esto borrará los registros actuales.')) {
            UserImport::importar();
            $this->info('¡Los usuarios se importaron correctamente!');
        } else {
            $this->info('La importación de usuarios fue cancelada.');
        }
    }
}
