<?php

namespace App\Http\Controllers\Admin;

use App\Models\Equipo;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EquipoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EquipoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Equipo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/equipo');
        CRUD::setEntityNameStrings('equipo', 'equipos');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */

         CRUD::column('CreadorNombre')->type('text')->label("Creado por");
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        // CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         // CRUD::field('user')->type('select');


         CRUD::field('nombre')->type('text');

         CRUD::field('slug')->type('text');

         CRUD::field('descripcion')->type('textarea');

         CRUD::field('imagen')->type('image_cover');

         CRUD::field('categoria')->type('text');

         CRUD::field('anuncio')->type('markdown_quill_simple');

         CRUD::field('reuniones')->type('markdown_quill_simple');

         CRUD::field('informacion')->type('markdown_quill_simple');

         CRUD::field('grupo')->type('select')->attributes([
            'readonly' => 'readonly'
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


    protected function show($id)
    {
        $equipo = Equipo::findOrFail($id);

        return redirect("/equipos/". $equipo->slug);
    }
}
