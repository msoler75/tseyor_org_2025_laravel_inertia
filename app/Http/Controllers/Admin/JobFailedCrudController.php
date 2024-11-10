<?php

namespace App\Http\Controllers\Admin;

use App\Models\JobFailed;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class JobCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class JobFailedCrudController extends CrudController
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
        CRUD::setModel(JobFailed::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/job-failed');
        CRUD::setEntityNameStrings('Tarea fallida', 'Tareas fallidas');
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
            'name' => 'failed_at',
            'type' => 'datetime',
            'label' => 'failed At',
        ]);

        // define a flush button:
        CRUD::addButtonFromView('top', 'retry_jobs', 'retry_jobs', 'end');
        CRUD::addButtonFromView('top', 'flush_jobs', 'flush_jobs', 'end');
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
