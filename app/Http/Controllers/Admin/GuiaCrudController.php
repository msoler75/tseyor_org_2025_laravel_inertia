<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class GuiaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class GuiaCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Guia::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/guia');
        CRUD::setEntityNameStrings('guia', 'guias');
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


         CRUD::column('nombre')->type('text');

         CRUD::column('descripcion')->type('text');

         CRUD::column('imagen')->type('image');

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
            'nombre' => 'required|min:2',
            // 'descripcion' => 'required|min:16',
            'imagen' =>'required'
        ]);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         CRUD::field('descripcion')->type('text');

         CRUD::field([   // select_from_array
            'name'        => 'categoria',
            'label'       => "Categoría",
            'type'        => 'select_from_array',
            'options'     => ['H1' => 'H1', 'H2' => 'H2', 'H3'=>'H3', 'Desconocido'=>'Desconocido', 'Otros' => 'Otros'],
            'allows_null' => false,
            'default'     => 'H2',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        CRUD::field('texto')->type('text_tinymce');

        CRUD::field('libros')->type('json')->hint('Libros de consulta de este Guía Estelar');

        $folder = 'media/guias';

        CRUD::field('imagen')->type('image_cover')->attributes(['from' => 'texto', 'folder' => $folder]);
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
        return redirect("/guias/$id");
    }
}
