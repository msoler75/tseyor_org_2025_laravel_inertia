<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Doctrine\Inflector\InflectorFactory;
use App\Models\Contenido;
use App\Pigmalion\ContenidoHelper;

class ContenidosImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contenidos:import {coleccion}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra todos los contenidos de la colección indicada, y los rehace desde ContenidoHelper::guardarContenido';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $this->info("$this->description");

        $coleccion = $this->argument('coleccion');

        if ($this->confirm('¿Está seguro de que desea recrear los contenidos? Esto borrará los registros actuales.')) {

            // Verificar si la clase del modelo existe
            $inflector = InflectorFactory::create()->build();
            $nombreSingular = $inflector->singularize($coleccion);
            $claseModelo = 'App\\Models\\' . ucfirst($nombreSingular);

            if (!class_exists($claseModelo)) {
                $this->error("La colección $coleccion no tiene un modelo asociado.");
                return;
            }

            // Borramos todos los contenidos de esa colección
            Contenido::where('coleccion', $coleccion)->delete();

            // Obtenemos todos los datos de esa colección
            $modelos = app()->make($claseModelo)::all();
            foreach ($modelos as $model) {
                ContenidoHelper::guardarContenido($model);
            }

            $this->info("Importación de contenidos completada para la colección $coleccion.");
        } else {
            $this->info('Operación cancelada.');
        }
    }
}
