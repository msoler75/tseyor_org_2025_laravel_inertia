<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Experiencia;
use Illuminate\Support\Facades\Storage;


/**
 * Class ExperienciaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ExperienciaCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\ReviseOperation\ReviseOperation;
    use \App\Traits\CrudContenido;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Experiencia::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/experiencia');
        CRUD::setEntityNameStrings('experiencia', 'experiencias');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */

         $this->crud->addColumn([
            'name'  => 'id',
            'label' => 'id',
            'type'  => 'number'
        ]);

        $this->crud->addColumn([
            'name'  => 'nombre',
            'label' => 'Nombre',
            'type'  => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime', // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);

        $this->crud->addColumn([
            'name'  => 'categoria',
            'label' => 'Categoría',
            'type'  => 'text',
        ]);


        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicada' : '⚠️ Borrador';
            }
        ]);
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation([
            // 'name' => 'required|min:2',
            'texto' => 'required|max:65000'
        ]);
        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field([
            // select_from_array
            'name' => 'categoria',
            'label' => "Tipo de experiencia",
            'type' => 'select_from_array',
            'options' =>  array_combine(array_keys(Experiencia::$categorias), array_keys(Experiencia::$categorias)),
            'allows_null' => false,
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper' => [
                'class' => 'form-group col-md-3'
            ],
        ]);

        CRUD::field('texto')->type('tiptap_editor_simple');

        CRUD::field('visibilidad')->type('visibilidad');

        CRUD::field('fecha')->label('Fecha de la experiencia')->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        CRUD::field([
            // Select
            'label' => "Usuario que creó la experiencia grupal",
            'type' => 'select',
            'name' => 'user_id',
            // the db column for the foreign key

            // optional
            // 'entity' should point to the method that defines the relationship in your Model
            // defining entity will make Backpack guess 'model' and 'attribute'
            'entity' => 'user',

            // optional - manually specify the related model and attribute
            'model' => "App\Models\User",
            // related model
            'attribute' => 'name',
            // foreign key attribute that is shown to user

            // optional - force the related options to be a custom query, instead of all();
            'options' => (function ($query) {
                return $query->orderBy('name', 'ASC')->get();
            }),
            //  you can use this to filter the results show in the select

            'wrapper' => [
                'class' => 'form-group col-md-3'
            ],

            'hint'=>'Déjalo en blanco si no es un trabajo de grupo'
        ]);

        $carpeta = "medios/experiencias/" . date("Y");

         // Verificar si la carpeta existe en el disco 'public'
         if (!Storage::disk('public')->exists($carpeta)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($carpeta);
        }

        CRUD::field([
            'label' => "Archivo adjunto",
            'type' => 'upload',
            'name' => 'archivo',
            'attributes' => [
                'accept' => "text/plain,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            ],
            'withFiles' => [
                'disk' => 'public',
                // the disk where file will be stored
                'path' => $carpeta,
                // the path inside the disk where file will be stored
            ]
        ]);

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


     public function show($id)
    {
        $experiencia = Experiencia::find($id);
        return $experiencia->visibilidad == 'P' ? redirect("/experiencias/$id") : redirect("/experiencias/$id?borrador");
    }
}
