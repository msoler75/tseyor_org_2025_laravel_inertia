<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Services\WordImport;
use App\Models\Publicacion;

/**
 * Class PublicacionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PublicacionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Publicacion::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/publicacion');
        CRUD::setEntityNameStrings('publicacion', 'publicaciones');
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
            'name'  => 'id',
            'label' => 'id',
            'type'  => 'number'
        ]);

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
            'type'  => 'text',
        ]);


        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
            }
        ]);

        CRUD::addButtonFromView('top', 'import_create', 'import_create', 'end');

        CRUD::addButtonFromView('line', 'import_update', 'import_update', 'beginning');

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
        CRUD::setValidation([
            'titulo' => 'required|min:8',
            'slug' => [ \Illuminate\Validation\Rule::unique('publicaciones', 'slug')->ignore($this->crud->getCurrentEntryId()) ],
            'descripcion' => 'max:400',
        ]);

        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field([   // select_from_array
            'name'        => 'categoria',
            'label'       => "Categoría",
            'type'        => 'select_from_array',
            'options'     => ['Retroalimentación' => 'Retroalimentación', 'Experiencias' => 'Experiencias', 'Mensajes' => 'Mensajes', 'Otros' => 'Otros'],
            'allows_null' => false,
            'default'     => 'General',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ])->after('titulo');

        $folder = "medios/publicaciones";

        CRUD::field('user_id')->type('select')->after('titulo')->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('equipo_id')->type('select')->after('titulo')->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength'=>400]);

        CRUD::field('slug')->type('text')->after('titulo')->hint('Puedes dejarlo en blanco');

        CRUD::field('texto')->type('tiptap_editor_simple')->attributes(['folder' => $folder]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

        CRUD::field('audios')->type('json');

        CRUD::field('visibilidad')->type('visibilidad');

        // se tiene que poner el atributo step para que no dé error el input al definir los segundos
        CRUD::field('published_at')->label('Fecha publicación')->type('datetime')->attributes(['step'=>1]);
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
        $publicacion = \App\Models\Publicacion::find($id);
        return $publicacion->visibilidad == 'P' ? redirect("/publicaciones/$id") : redirect("/publicaciones/$id?borrador");
    }

    public function importCreate()
    {
        $contenido = Publicacion::create([
            "titulo" => "Importado de " . $_FILES['file']['name'] . "_" . substr(str_shuffle('0123456789'), 0, 5),
            "texto" => ""
        ]);
        return $this->importUpdate($contenido->id);
    }


    public function importUpdate($id)
    {
        $contenido = Publicacion::findOrFail($id);

        try {
            // inicializa el importador en base a $_FILES
            $imported = new WordImport();

            // copia las imágenes desde la carpeta temporal al directorio destino, sobreescribiendo las anteriores en la carpeta
            $imported->copyImagesTo($this->getMediaFolder($contenido), true);

            // ahora las imagenes están con la nueva ubicación
            $contenido->texto = $imported->content;

            $contenido->save();

            return response()->json([
                "id" => $contenido->id
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
