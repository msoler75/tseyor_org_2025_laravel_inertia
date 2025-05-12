<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
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
        // Ajustar el widget para reflejar el contexto del modelo Suscriptor
        Widget::add()->to('before_content')->type('div')->class('row')->content([
            Widget::make()
                ->class('card mb-2')
                ->content([
                    'body' => 'Gestión de suscriptores al boletín Tseyor.'
                ])
        ]);

        // Configurar columnas existentes en el modelo Suscriptor
        CRUD::column('id')->type('number')->label('ID');
        CRUD::column('email')->type('email')->label('Correo Electrónico');
        CRUD::column('servicio')->type('text')->label('Servicio');
        CRUD::column('estado')->type('text')->label('Estado');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'email' => 'required|email|unique:suscriptores,email',
            'servicio' => 'required|string',
            'estado' => 'required|string',
        ]);

        // Configurar campos existentes en el modelo Suscriptor
        CRUD::field('email')->type('email')->label('Correo Electrónico');
        CRUD::field('servicio')->type('text')->label('Servicio');
        CRUD::field('estado')->type('text')->label('Estado');
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
