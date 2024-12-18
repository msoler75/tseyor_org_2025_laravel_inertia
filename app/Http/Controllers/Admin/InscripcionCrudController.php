<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;

/**
 * Class InscripcionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InscripcionCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    // use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
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
        CRUD::setModel(\App\Models\Inscripcion::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/inscripcion');
        CRUD::setEntityNameStrings('inscripción', 'inscripciones');
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

        CRUD::column('created_at')->type('closure')->label('Fecha')
            ->function(function ($entry) {
                // Suponiendo que $record es un modelo que contiene el campo de fecha y hora
                $dateTime = Carbon::parse($entry->created_at);
                $humanReadableDateTime = $dateTime->diffForHumans();
                return $humanReadableDateTime;
            });
        CRUD::column('estado')->type('text');
        CRUD::column('nombre')->type('text');
        // CRUD::column('region')->type('text');
        CRUD::column('pais')->type('text');
        CRUD::column('email')->type('email');
        CRUD::column('asignado')->type('text');
        // CRUD::column('comentario')->type('text');
        // CRUD::column('created_at')->type('datetime');

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
        // $this->crud->setValidation(InscripcionRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        // campo estado que es un enum
        /*
        ALTER TABLE inscripciones
        ADD COLUMN estado ENUM('nuevo','asignado','no contesta','en curso') NULL DEFAULT 'nuevo' COLLATE utf8mb4_general_ci,
        ADD COLUMN asignado VARCHAR(256) NULL DEFAULT NULL COLLATE utf8mb4_general_ci,
        ADD COLUMN notas TEXT NULL DEFAULT NULL COLLATE utf8mb4_general_ci;
        */

        CRUD::field('estado')->type('enum')->options(['nuevo' => 'nuevo', 'asignado' => 'asignado', 'no contesta' => 'no contesta', 'contactado' => 'contactado', 'en curso' => 'en curso',  'duplicado' => 'duplicado'])->default('nuevo')
            ->wrapper([
                'class' => 'form-group col-md-3'
            ])->before('nombre');

        // campo asignado, max length 256
        CRUD::field('asignado')->type('text')->attributes(['maxlength' => 256])->label('Asignado a');

        // campo notas
        CRUD::field('notas')->type('textarea')->label("Notas para el seguimiento");
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
