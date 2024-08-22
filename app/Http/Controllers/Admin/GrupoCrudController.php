<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Grupo;

/**
 * Class GrupoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GrupoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
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
        CRUD::setModel(\App\Models\Grupo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/grupo');
        CRUD::setEntityNameStrings('grupo', 'grupos');
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
            'name' => 'nombre',
            'label' => 'Nombre',
        ]);

        $this->crud->addColumn([
            'name' => 'slug',
            'label' => 'Slug',
        ]);


        $this->crud->addColumn([
            'label' => 'Creado en',
            'type' => 'datetime',
            'name' => 'created_at',
        ]);

         CRUD::column('usuarios')->type('relationship_count');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */



         CRUD::addField([
            'name' => 'nombre',
            'type'      => 'text',
            'wrapper' => ['maxlength ' => '32'],
        ]);

        CRUD::addField([
            'name' => 'slug',
            'type'      => 'text',
            'wrapper' => ['maxlength ' => '32'],
        ]);

        CRUD::addField([
            'name' => 'descripcion',
            'type'      => 'textarea',
            'wrapper' => ['maxlength ' => '400'],
        ]);

         /* CRUD::field('usuarios')->type('select_multiple')
         ->wrapper(); */

        /* CRUD::addField([
            'name' => 'usuarios',
            'type'      => 'select_multiple',
            'wrapper' => ['class' => 'form-group col-md-4'],
        ]);*/

        $this->crud->addField([
            'name'              => 'UsuariosJSON',
            'label'             => 'Usuarios',
            'type'              => 'select_model',
            'model' => 'user',
            'options' => null,
            'multiple' => true,
            'hint' => 'Pulsa espacio para cargar todos los usuarios, o escribe para buscar'
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




      /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run

        $r = $this->traitStore();

        $this->actualizarUsuarios($this->crud->entry->id, $this->crud->getRequest()->UsuariosJSON);

        return $r;
    }


     /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run

        $r = $this->traitUpdate();

        $this->actualizarUsuarios($this->crud->entry->id, $this->crud->getRequest()->UsuariosJSON);

        return $r;
    }



    /**
     * Actualiza los miembros del equipo y sus roles
     */
    protected function actualizarUsuarios($grupo_id, $usuarios)
    {
        $grupo = Grupo::findOrFail($grupo_id);

        // Convertir la cadena en un arreglo de IDs
        $usuarios_ids = array_column(json_decode($usuarios, true), 'value');

        // Sincronizar los usuarios (esto eliminará los existentes y añadirá los nuevos)
        $grupo->usuarios()->sync($usuarios_ids);
    }


}
