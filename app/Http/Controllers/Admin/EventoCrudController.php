<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;

/**
 * Class EventoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EventoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Evento::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/evento');
        CRUD::setEntityNameStrings('evento', 'eventos');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'titulo' => 'required|min:8',
            'descripcion' => 'required|max:400'
        ]);
        // CRUD::setValidation(EntradaRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        $folder = $this->mediaFolder();

        CRUD::field('descripcion')->type('textarea');

        CRUD::field('texto')->type('markdown_quill')->attributes(['folder' => $folder]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

        CRUD::field('visibilidad')->type('visibilidad');


        CRUD::field([   // select_from_array
            'name'        => 'categoria',
            'label'       => "Categoría",
            'type'        => 'select_from_array',
            'options' => ['Curso' => 'Curso', 'Convivencias' => 'Convivencias', 'Encuentro' => 'Encuentro', 'Presentación' => 'Presentación'],
            'allows_null' => false,
            'default'     => 'Encuentro',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        CRUD::field([
            'name' => 'centro',
            'label' => 'Centro',
            'allows_null' => true,
            'type' => 'select',
            'attribute'    => 'nombre',
            'model'       => 'App\Models\Centro',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ]
        ]);

        CRUD::field([
            'name' => 'sala',
            'label' => 'Sala virtual',
            'allows_null' => true,
            'type' => 'select',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ]
        ]);

        CRUD::field([
            'name' => 'equipo',
            'label' => 'Equipo organizador',
            'allows_null' => true,
            'type' => 'select',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ]
        ]);

        CRUD::field('visibilidad')->type('visibilidad');
    }


    private function mediaFolder()
    {
        $anioActual = date('Y');
        $mesActual = date('m');

        $folder = "/media/eventos/$anioActual/$mesActual";

        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk('public')->exists($folder)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($folder);
        }

        return $folder;
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
