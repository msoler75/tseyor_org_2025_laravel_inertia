<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Hash extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hash {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calcula el código hash de un archivo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $archivo = $this->argument('file');

        if(!$archivo) {
            die("Se debe especificar el archivo");
        }

        if(!file_exists($archivo))
        die("No se ha encontrado el archivo especificado");

        $hash = md5_file($archivo);

        echo "hash (md5): $hash". PHP_EOL;
        echo "tamaño: ".filesize($archivo) . " bytes".PHP_EOL;
    }
}
