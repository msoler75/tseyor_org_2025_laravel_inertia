<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Suscriptor;

class SuscriptorCrudController extends CrudController
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
        CRUD::setModel(Suscriptor::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/suscriptor');
        CRUD::setEntityNameStrings('suscriptor', 'suscriptores');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::column('id')->type('number')->label('ID');
        CRUD::column('nombre')->type('text')->label('Nombre');
        CRUD::column('email')->type('email')->label('Correo Electrónico');
        CRUD::column('activo')->type('boolean')->label('Activo');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'nombre' => 'required|string|max:255',
            'email' => 'required|email|unique:suscriptores,email',
            'activo' => 'boolean',
        ]);

        CRUD::field('nombre')->type('text')->label('Nombre');
        CRUD::field('email')->type('email')->label('Correo Electrónico');
        CRUD::field('activo')->type('boolean')->label('Activo');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}
