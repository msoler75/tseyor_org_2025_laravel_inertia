<?php

namespace App\Http\Controllers\Admin;

use App\Models\Nodo;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class NodoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NodoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Nodo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/nodo');
        CRUD::setEntityNameStrings('nodo', 'nodos');
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


         //CRUD::field('user')->remove();
         //CRUD::field('group')->remove();

         $this->crud->addColumn([
            'name' => 'ruta',
            'label' => 'Ruta',
            'limit'=>200
        ]);

        /*$this->crud->addColumn([
            'label' => 'Creado en',
            'type' => 'datetime',
            'name' => 'created_at',
        ]);


         $this->crud->addColumn([
            'name' => 'NombreUsuario',
            'label' => 'Usuario',
            'model'       => 'App\Models\User',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('user', function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%');
                });
            },
            'orderable'   => true,
            'orderLogic'  => function ($query, $column, $columnDirection) {
                return $query->leftJoin('users', 'nodos.user_id', '=', 'users.id')
                    ->orderBy('users.name', $columnDirection)->select('nodos.*');
            },
        ]);

        $this->crud->addColumn([
            'name' => 'NombreGrupo',
            'label' => 'Grupo',
            'model'       => 'App\Models\Grupo',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('group', function ($q) use ($searchTerm) {
                    $q->where('nombre', 'like', '%' . $searchTerm . '%');
                });
            },
            'orderable'   => true,
            'orderLogic'  => function ($query, $column, $columnDirection) {
                return $query->leftJoin('users', 'nodos.user_id', '=', 'users.id')
                    ->orderBy('users.name', $columnDirection)->select('nodos.*');
            },
        ]);
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
            'ruta' => 'required|min:2',
            'permisos' => 'required',
        ]);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         CRUD::field('user_id')->type('select')->wrapper(['class' => 'form-group col-md-3']);
         CRUD::field('group_id')->type('select')->wrapper(['class' => 'form-group col-md-3']);
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
        $nodo = Nodo::findOrFail($id);

        return redirect("/". $nodo->ruta);
    }
}
