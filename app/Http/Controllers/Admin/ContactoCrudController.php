<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Contacto;
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
    use \App\Traits\CrudContenido;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Contacto::class);
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
            'name' => 'nombre',
            'label' => 'Nombre',
            'type' => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime', // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);

        $this->crud->addColumn([
            'name' => 'imagen',
            'label' => 'Imagen',
            'type' => 'image',
            'value' => function ($entry) {
                return $entry->imagen . '?mh=25';
            }
        ]);

        $this->crud->addColumn([
            'name' => 'nombrePais',
            'label' => 'País',
            'type' => 'text',
        ]);


        $this->crud->addColumn([
            'name' => 'visibilidad',
            'label' => 'Estado',
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
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
            'slug' => [ \Illuminate\Validation\Rule::unique('contactos', 'slug')->ignore($this->crud->getCurrentEntryId()) ],
            'imagen' => 'required|max:255',
            'pais' => 'required',
            'provincia' => 'required|max:64',
            'direccion' => 'max:255',
            'codigo' => 'max:16',
            'telefono' => 'max:64',
            'poblacion'=>'required|max:128',
            'social' => 'max:255'
        ]);

        CRUD::setFromDb(); // set columns from db columns.

        $folder = $this->getMediaFolder();

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder])->hint('Imagen que se verá en el mapa')->after('slug')
        ->hint('Imagen que se muestra en el mapa de contactos');

        CRUD::field('direccion')->hint('Calle y número');

        CRUD::field('codigo')->label('Código postal')->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        CRUD::field('social')->label('Enlace a una red social');

        CRUD::field('slug')->hint('Nombre corto para los enlaces. No lo rellenes si no sabes como funciona');

        CRUD::field('latitud')->type('number')->attributes(['step' => 'any'])
        ->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        CRUD::field('longitud')->type('number')->attributes(['step' => 'any'])
        ->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        CRUD::field('xxxx')->type('gmaps_coords')->after('longitud');

        CRUD::field([   // select_from_array
            'name' => 'pais',
            'label' => "País",
            'type' => 'select_from_array',
            'options' => Countries::$list,
            'allows_null' => false,
            'default' => 'ES',
            'wrapper' => [
                'class' => 'form-group col-md-3'
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

    public function show($id)
    {
        $contacto = Contacto::find($id);
        return $contacto->visibilidad == 'P' ? redirect("/contactos/$id") : redirect("/contactos/$id?borrador");
    }
}
