<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;


/**
 * Class TerminCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class TerminoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Termino::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/termino');
        CRUD::setEntityNameStrings('término', 'términos');
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


   //add div row using 'div' widget and make other widgets inside it to be in a row
   Widget::add()->to('before_content')->type('div')->class('row')->content([

    //widget made using fluent syntax
    Widget::make()
        ->type('card')
        ->class('card bg-dark text-white mb-1') // optional
        ->content([
            'body'=>'Términos del glosario. No se incluyen términos de Guías Estelares ni de Lugares, como bases o planetas, porque estos van en sus propios tipos de contenidos.'
        ])


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
        CRUD::setValidation([
            'titulo' => 'required|min:8',
            'descripcion' => 'required|max:400'
        ]);
        // CRUD::setValidation(EntradaRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         $folder = "/media/terminos";

         CRUD::field('descripcion')->type('textarea');

         CRUD::field('texto')->type('markdown_quill')->attributes(['folder' => $folder])->after('descripcion');

         CRUD::field('visibilidad')->type('visibilidad');
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
