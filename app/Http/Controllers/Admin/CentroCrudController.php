<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Centro;
use App\Pigmalion\Countries;

/**
 * Class CentroCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CentroCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\ReviseOperation\ReviseOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Centro::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/centro');
        CRUD::setEntityNameStrings('centro', 'centros');
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
            'name'  => 'imagen',
            'label' => 'Imagen',
            'type'  => 'image'
        ]);


        $this->crud->addColumn([
            'name'  => 'nombrePais',
            'label' => 'País',
            'type'  => 'text',
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
        CRUD::setValidation([
            'nombre' => 'required|min:8',
            'slug' => [ \Illuminate\Validation\Rule::unique('centros', 'slug')->ignore($this->crud->getCurrentEntryId()) ],
            'descripcion' => 'required|max:400',
            'imagen' => 'required'
        ]);

        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        $folder = "/medios/centros";

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder]);

        CRUD::field([   // select_from_array
            'name'        => 'pais',
            'label'       => "País",
            'type'        => 'select_from_array',
            'options'     => Countries::$list,
            'allows_null' => false,
            'default'     => 'ES',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ]);


        CRUD::field([
            'name' => 'contacto',
            'label' => 'Contacto',
            'allows_null' => true,
            'type' => 'select',
            'attribute'    => 'nombre',
            'model'       => 'App\Models\Contacto',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
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
        $centro = Centro::find($id);
        return $centro->visibilidad == 'P' ? redirect("/centros/$id") : redirect("/centrods/$id?borrador");
    }
}
