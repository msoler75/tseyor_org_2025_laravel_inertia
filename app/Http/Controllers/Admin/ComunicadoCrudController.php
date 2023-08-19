<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;

/**
 * Class ComunicadoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class ComunicadoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Comunicado::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/comunicado');
        CRUD::setEntityNameStrings('comunicado', 'comunicados');
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
            'name'  => 'titulo',
            'label' => 'Título',
            'type'  => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime', // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);


        $this->crud->addColumn([
            'name'  => 'categoria',
            'label' => 'Categoría',
            'type'  => 'enum',
            'options'     => ['GEN' => 'General', 'TAP' => 'TAP', 'DOCEM' => 'Doce del Muulasterio', 'MUUL' => 'Muul']
        ]);

        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'enum',
            'options' => [
                'B' => '⚠️ Borrador',
                'P' => '✔️ Publicado'
            ]
        ]);

        $this->crud->addButtonFromView('top', 'import', 'import', 'end');
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
        ]);

        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field([
            'name' => 'numero',
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::field([   // select_from_array
            'name'        => 'categoria',
            'label'       => "Categoría",
            'type'        => 'select_from_array',
            'options'     => ['GEN' => 'General', 'TAP' => 'TAP', 'DOCEM' => 'Doce del Muulasterio', 'MUUL' => 'Muul'],
            'allows_null' => false,
            'default'     => 'GEN',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        $folder = $this->mediaFolder();

        // CRUD::field('texto')->type('markdown_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('texto')->type('markdown_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

        CRUD::field('visibilidad')->type('visibilidad');
    }

    private function mediaFolder()
    {
        $anioActual = date('Y');
        $mesActual = date('m');

        $folder = "/media/comunicados/$anioActual/$mesActual";

        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk('public')->exists($folder)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk('public')->makeDirectory($folder);
        }

        return $folder;
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
        return redirect("/comunicados/$id?borrador");
    }

    // show whatever you want
    protected function setupShowOperation2()
    {
        // MAYBE: do stuff before the autosetup

        // automatically add the columns
        // $this->autoSetupShowOperation();

        $id = 0;
        if (preg_match('/\/(\d+)\/show$/', $_SERVER['REQUEST_URI'], $match))
            $id = $match[1];

        // https://backpackforlaravel.com/docs/6.x/crud-columns#custom_html-1
        if ($id)
            $this->crud->addColumn(
                [
                    'name'     => 'my_custom_html',
                    'label'    => 'Ver en Web',
                    'type'     => 'custom_html',
                    'value'    => "<a href='/comunicados/$id?borrador' target='_blank'>➡️ Ver Comunicado en el Sitio Web</a>"
                ]
            );

        CRUD::column('titulo')->type('text');
        CRUD::column('numero')->type('number');
        CRUD::column('categoria')->type('text');
        CRUD::column('descripcion')->type('textarea');
        CRUD::column('texto')->type('mymarkdown');
        CRUD::column('imagen')->type('image');

        $this->crud->addColumn([
            'name' => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'enum',
            'options' => [
                'B' => '⚠️ Borrador',
                'P' => '✔️ Publicado'
            ]
        ]);

        // MAYBE: do stuff after the autosetup


        // or maybe remove a column
        // CRUD::column('text')->remove();
    }


    public function import()
    {
        // whatever you decide to do
    }
}
