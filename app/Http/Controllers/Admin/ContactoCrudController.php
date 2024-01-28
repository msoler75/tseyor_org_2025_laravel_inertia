<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Pigmalion\Countries;




/**
 * Class ContactoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ContactoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Contacto::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/contacto');
        CRUD::setEntityNameStrings('contacto', 'contactos');
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


        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'text',
            'value' => function($entry) {
                return $entry->visibilidad == 'P'?'✔️ Publicado':'⚠️ Borrador';
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
        CRUD::setValidation([
            'nombre' => 'required',
            'imagen' => 'required',
            'pais' => 'required',
            'provincia' => 'required',
        ]);

        CRUD::setFromDb(); // set columns from db columns.

        $folder = "/medios/contactos";

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder]);

        CRUD::field('slug')->hint('Nombre corto para los enlaces. No lo rellenes si no sabes como funciona');

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

        CRUD::field('visibilidad')->type('visibilidad');
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

    protected function show($id)
    {
        return redirect("/contactos/$id?borrador");
    }
}
