<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use App\Models\Libro;

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
    use \Backpack\ReviseOperation\ReviseOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Libro::class);
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
        $this->crud->addColumn([
            'name' => 'titulo',
            'label' => 'Título',
            'type' => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime',
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
        CRUD::setValidation([
            'titulo' => 'required|min:8',
            'descripcion' => 'required|max:2048',
            'imagen' => 'required',
            'pdf' => 'required|mimes:pdf',
        ]);
        // CRUD::setValidation(EntradaRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        $folderImages = "/medios/libros/portadas";

        $folderPDF = "/medios/libros/pdf";

        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk('public')->exists($folderImages)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($folderImages);
        }

        if (!Storage::disk('public')->exists($folderPDF)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($folderPDF);
        }


        CRUD::field('categoria')->hint('Monografías, Obras de referencia, Cuentos, Talleres... Se pueden poner varias categorías separadas por coma.');

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength'=>400]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folderImages]);


        CRUD::field('edicion')->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        CRUD::field('paginas')->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        CRUD::field('visibilidad')->type('visibilidad');

        CRUD::field('pdf')->label('Archivo PDF')
            ->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => $folderPDF, // the path inside the disk where file will be stored
            ])->attributes([
                    'accept' => '.pdf',
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


    public function show($id)
    {
        $libro = Libro::find($id);
        return $libro->visibilidad == 'P' ? redirect("/libros/$id") : redirect("/libros/$id?borrador");
    }
}
