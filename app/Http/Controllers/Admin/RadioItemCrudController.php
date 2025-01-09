<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;


/**
 * Class RadioItemCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class RadioItemCrudController extends CrudController
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
        CRUD::setModel(\App\Models\RadioItem::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/radio-item');
        CRUD::setEntityNameStrings('Radio item', 'Listado Radio Tseyor');
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

        CRUD::column('id');

        CRUD::column('titulo');

        CRUD::column('categoria')->label('emisora');

        CRUD::column('duracion');

        // CRUD::column('desactivado')->label('desactivado')->type("check");


        $this->crud->addColumn([
            'name' => 'audio_player',
            'label' => 'Audio',
            'type' => 'view',
            'view' => 'vendor.backpack.crud.columns.audio_player'
        ]);

        $this->crud->addColumn([
            'name' => 'is_active',
            'label' => 'Activo',
            'type' => 'view',
            'view' => 'vendor.backpack.crud.columns.toggle_switch'
        ]);

        CRUD::setOperationSetting('lineButtonsAsDropdown', true);
        CRUD::addButtonFromModelFunction('top', 'audio_player', 'audioPlayer', 'beginning');
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
            'titulo' => 'required',
            'url' => 'required',
            'duracion' => 'required',
            'categoria' => 'required'
        ]);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        CRUD::field('titulo')->type('text');

        CRUD::field('url')->type('text')->hint('URL del archivo de audio');

        CRUD::field('duracion')->type('text')->wrapper([
            'class' => 'form-group col-md-3'
        ])->hint('Se puede poner la duración en formato HH:MM:SS ó MM:SS ó el tiempo total en segundos');

        CRUD::field([
            // select_from_array
            'name' => 'categoria',
            'label' => "Categoría",
            'type' => 'select_from_array',
            'options' => ['Comunicados' => 'Comunicados', 'Talleres y meditaciones' => 'Talleres y meditaciones', 'Entrevistas y mesas redondas' => 'Entrevistas y mesas redondas', 'Jingles' => 'Jingles'],
            'allows_null' => false,
            'default' => '0',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ],
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
     * Para activar o desacticar el item desde la lista de administración
     */
    public function toggle(Request $request, $id)
    {
        $radioItem = $this->crud->model::findOrFail($id);
        $radioItem->desactivado = !$request->input('active');
        $radioItem->save();

        return response()->json(['success' => true]);
    }
}
