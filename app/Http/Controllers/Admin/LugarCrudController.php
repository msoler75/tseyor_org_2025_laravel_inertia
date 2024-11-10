<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class LugarCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LugarCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\ReviseOperation\ReviseOperation;
    use \App\Traits\CrudContenido;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Lugar::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/lugar');
        CRUD::setEntityNameStrings('lugar', 'lugares de la galaxia');
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
            'name' => 'id',
            'label' => 'id',
            'type' => 'number'
        ]);

        $this->crud->addColumn([
            'name' => 'nombre',
            'label' => 'Nombre',
            'type' => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime',
            // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);

        $this->crud->addColumn([
            'name' => 'categoria',
            'label' => 'Categoría',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'visibilidad',
            'label' => 'Estado',
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
            }
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
        // $this->crud->setValidation(LugarRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */


         $folder = $this->getMediaFolder();

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength' => 400])->hint('descripción corta');

        // CRUD::field('texto')->type('text_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('texto')->type('tiptap_editor')->attributes(['folder' => $folder]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto'])->after('texto');

        CRUD::field('relacionados')->type('textarea')->hint('slug de otros lugares de la galaxia, separados por comas o saltos de linea. Ejemplo: planeta-agguniom, planeta-albus, etc.')->attributes(['rows' => 4]);

        CRUD::field('libros')->type('textarea')->hint('slug de libros, separados comas o saltos de linea. Ejemplo: los-guias-estelares, el-rayo-sincronizador, ...')->attributes(['rows' => 4]);

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


    public function show($id)
    {
        $lugar = \App\Models\Lugar::find($id);
        return $lugar->visibilidad == 'P' ? redirect("/lugares/$id") : redirect("/lugares/$id?borrador");
    }
}
