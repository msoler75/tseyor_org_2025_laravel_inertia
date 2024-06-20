<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Imports\RadioImport;

class ImportRadio extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:radio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa los audios para la radio Tseyor desde: comunicados, biblioteca/audios, y jingles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("$this->description");
        if ($this->confirm('¿Está seguro de que desea importar los audios? Esto borrará los registros actuales.')) {
            RadioImport::importar();
            $this->info('¡Los audios se importaron correctamente!');
        } else {
            $this->info('La importación de audios de radio fue cancelada.');
        }
    }
}
