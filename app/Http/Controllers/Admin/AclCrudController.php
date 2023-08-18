<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\AclRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class AclCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AclCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Acl::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/acl');
        CRUD::setEntityNameStrings('acl', 'acls');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */



        $this->crud->addColumn([
            'name' => 'RutaNodo',
            'label' => 'Ruta',
            'model'       => 'App\Models\Nodo',
            'searchLogic' => function ($query, $column, $searchTerm) {
                $query->orWhereHas('nodo', function ($q) use ($searchTerm) {
                    $q->where('ruta', 'like', '%' . $searchTerm . '%');
                });
            },
            'orderable'   => true,
            'orderLogic'  => function ($query, $column, $columnDirection) {
                return $query->leftJoin('nodos', 'nodos_acl.nodo_id', '=', 'nodos.id')
                    ->orderBy('nodos.ruta', $columnDirection)->select('nodos_acl.*');
            },
        ]);


        $this->crud->addColumn([
            'label' => 'Creado en',
            'type' => 'datetime',
            'name' => 'created_at',
        ]);

        $this->crud->addColumn([
            'name' => 'NombreUsuario',
            'label' => 'Usuario',
            'orderable'   => true,
        ]);

        $this->crud->addColumn([
            'name' => 'NombreGrupo',
            'label' => 'Grupo',
            'orderable'   => true,
        ]);


        $this->crud->addColumn([
            'name' => 'verbos',
            'label' => 'Verbos',
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
        CRUD::setValidation(AclRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */
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
