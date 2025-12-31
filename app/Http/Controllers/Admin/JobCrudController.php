<?php

namespace App\Http\Controllers\Admin;

use App\Models\Job;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class JobCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class JobCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
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
        CRUD::setModel(Job::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/job');
        CRUD::setEntityNameStrings('Tarea (pendiente)', 'Tareas (pendientes)');
    }

      /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        // Define the columns for the list view

          // Define the columns for the list view
          CRUD::addColumn([
            'name' => 'id',
            'type' => 'text',
            'label' => 'Id',
        ]);

        CRUD::addColumn([
            'name' => 'queue',
            'type' => 'text',
            'label' => 'Queue',
        ]);
        CRUD::addColumn([
            'name' => 'display',
            'type' => 'text',
            'label' => 'display',
        ]);
        CRUD::addColumn([
            'name' => 'data',
            'type' => 'text',
            'label' => 'data',
        ]);
        CRUD::addColumn([
            'name' => 'available_at',
            'type' => 'datetime',
            'label' => 'Available At',
        ]);

        // Optionally, you can define filters, buttons, etc.

        CRUD::addButtonFromView('top', 'detect_audios_to_process', 'detect_audios_to_process', 'start');

        // CRUD::addButtonFromView('top', 'worker_buttons', 'worker_buttons', 'end');
    }


    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation([
            // 'name' => 'required|min:2',
        ]);
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
