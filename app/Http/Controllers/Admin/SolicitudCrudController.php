<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class SolicitudCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class SolicitudCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Solicitud::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/solicitud');
        CRUD::setEntityNameStrings('solicitud', 'solicitudes');
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
            'name' => 'created_at',
            'label' => 'Realizada',
            'orderable'   => true,
        ]);

         $this->crud->addColumn([
            'name' => 'NombreUsuario',
            'label' => 'Usuario',
            'orderable'   => true,
        ]);

        $this->crud->addColumn([
            'name' => 'NombreEquipo',
            'label' => 'Equipo',
            'orderable'   => true,
        ]);

        $this->crud->addColumn([
            'name' => 'fecha_aceptacion',
            'label' => 'Aceptado',
            'type' => 'datetime'
        ]);

        $this->crud->addColumn([
            'name' => 'fecha_denegacion',
            'label' => 'Denegado',
            'type' => 'datetime'
        ]);

        $this->crud->addColumn([
            'name' => 'NombreCoordinador',
            'label' => 'Coordinador',
        ]);


        CRUD::setOperationSetting('lineButtonsAsDropdown', true);

    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        // CRUD::setValidation(SolicitudRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         CRUD::field('user_id')->attributes(['disabled'=>'disabled']);


         CRUD::field('equipo_id')->type('select')->model('App\Models\Equipo');
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
