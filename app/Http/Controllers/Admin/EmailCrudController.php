<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Email;

/**
 * Class EmailCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EmailCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Email::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/email');
        CRUD::setEntityNameStrings('email', 'emails');
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
            'name'  => 'tituloContenido',
            'label' => 'Contenido',
            'type'  => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'coleccion',
            'label' => 'Colección',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'operacion',
            'label' => 'Operación',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'autor',
            'label' => 'Autor',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Fecha',
            'type' => 'datetime',
        ]);

    }


    protected function show($id)
    {
        return redirect("/emails/$id");
    }
}
