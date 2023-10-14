<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use App\Services\WordImport;
use App\Models\Noticia;


/**
 * Class NoticiaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class NoticiaCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Noticia::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/noticia');
        CRUD::setEntityNameStrings('noticia', 'noticias');
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
            'type' => 'datetime',
        ]);


        /* $this->crud->addColumn([
            'name'  => 'categoria',
            'label' => 'Categoría',
            'type'  => 'text',
        ]);
        */

        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'text',
            'value' => function($entry) {
                return $entry->visibilidad == 'P'?'✔️ Publicado':'⚠️ Borrador';
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
            'descripcion' => 'required|max:400'
        ]);
        // CRUD::setValidation(EntradaRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         $folder = $this->mediaFolder();

         CRUD::field('descripcion')->type('textarea');

         CRUD::field('texto')->type('markdown_quill')->attributes(['folder' => $folder]);

         CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

         CRUD::field('visibilidad')->type('visibilidad');
    }


    private function mediaFolder()
    {
        $anioActual = date('Y');
        $mesActual = date('m');

        $folder = "/media/noticias/$anioActual/$mesActual";

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
        return redirect("/noticias/$id?borrador");
    }



    public function importCreate()
    {
        try {

            $imported = new WordImport();

            $contenido = Noticia::create([
                "titulo" => "Importado de ". $_FILES['file']['name'] . "_". substr(str_shuffle('0123456789'), 0, 5),
                "texto" => $imported->content
            ]);

            // Copiaremos las imágenes a la carpeta de destino
            $imagesFolder = "media/noticias/_{$contenido->id}";

            // copia las imágenes desde la carpeta temporal al directorio destino
            $imported->copyImagesTo($imagesFolder);

            // reemplazar la ubicación de las imágenes en el texto del comunicado
            $contenido->texto = preg_replace("/\bmedia\//", "$imagesFolder/", $contenido->texto);
            $contenido->texto = preg_replace("/\.\/media\//", "/storage/media/", $contenido->texto);

            $contenido->imagen = preg_replace("/\bmedia\//", "$imagesFolder/", $contenido->imagen);
            $contenido->imagen = preg_replace("/\.\/media\//", "/storage/media/", $contenido->imagen);
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



    public function importUpdate($id)
    {
        try {
            $imported = new WordImport();

            $contenido = Noticia::findOrFail($id);

            $contenido->texto = $imported->content;

            // Copiaremos las imágenes a la carpeta de destino
            $imagesFolder = "media/noticias/_{$contenido->id}";

            // reemplazar la ubicación de las imágenes en el texto del comunicado
            $contenido->texto = preg_replace("/\bmedia\//", "$imagesFolder/", $contenido->texto);
            $contenido->texto = preg_replace("/\.\/media\//", "/storage/media/", $contenido->texto);

            $contenido->descripcion = null; // para que se regenere

            $contenido->imagen = null; // para que se elija otra nueva, si la hay
            $contenido->save();

            // copia las imágenes desde la carpeta temporal al directorio destino, sobreescribiendo las anteriores en la carpeta
            $imported->copyImagesTo($imagesFolder, true);

            return response()->json([], 200);
        } catch (\Exception $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
