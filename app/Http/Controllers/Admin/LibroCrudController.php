<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;

/**
 * Class LibroCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class LibroCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Libro::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/libro');
        CRUD::setEntityNameStrings('libro', 'libros');
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

        $folderImages = "/media/libros";

        $folderPDF = "/media/libros/pdf";

        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk('public')->exists($folderImages)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($folderImages);
        }

        if (!Storage::disk('public')->exists($folderPDF)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($folderPDF);
        }

        CRUD::field('descripcion')->type('textarea');

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folderImages]);

        CRUD::field('visibilidad')->type('visibilidad');

        CRUD::field(
            [
                'name' => 'pdf',
                'label' => 'Archivo PDF',
                'type' => 'upload',
                'upload' => true,
                'upload_folder' => $folderPDF,
                'attributes' => [
                    'accept' => '.pdf',
                ],
            ]
        );
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
