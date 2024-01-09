<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use App\Services\WordImport;
use App\Models\Comunicado;
use App\Http\Requests\StoreComunicadoRequest;
use App\Jobs\ProcesarAudios;


// esto permite testar la conversión de audio al guardar el comunicado
define('TESTAR_CONVERTIDOR_AUDIO2', false);

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
        CRUD::setModel(Comunicado::class);
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
            'name' => 'id',
            'label' => 'id',
            'type' => 'number'
        ]);

        $this->crud->addColumn([
            'name' => 'titulo',
            'label' => 'Título',
            'type' => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime',
            // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);

        $this->crud->addColumn([
            'name' => 'categoriaNombre',
            'label' => 'Categoría',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'numero',
            'label' => 'Numero',
            'type' => 'text',
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        $this->crud->addColumn([
            'name' => 'visibilidad',
            'label' => 'Estado',
            'type' => 'text',
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
        CRUD::setValidation(StoreComunicadoRequest::class);
        /*CRUD::setValidation([
            'titulo' => 'required|min:8',
            'texto'=>'required',
            'descripcion' => 'max:400',
        ]);*/

        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field([
            'name' => 'numero',
            'wrapper' => [
                'class' => 'form-group col-md-3'
            ]
        ]);

        CRUD::field([
            // select_from_array
            'name' => 'categoria',
            'label' => "Categoría",
            'type' => 'select_from_array',
            'options' => ['0' => 'General', '1' => 'TAP', '2' => 'Doce del Muulasterio', '3' => 'Muul'],
            'allows_null' => false,
            'default' => '0',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper' => [
                'class' => 'form-group col-md-3'
            ],
        ]);

        $folder = $this->mediaFolder();

        CRUD::field('descripcion')->type('textarea');

        // CRUD::field('texto')->type('text_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('texto')->type('text_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);


        CRUD::field('ano')->type('number')->attributes(['min' => 2001, 'max' => 2054])->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        CRUD::field('fecha_comunicado')->wrapper([
            'class' => 'form-group col-md-3'
        ]);

        // CRUD::field('audios')->type('json');

        CRUD::field([
            'name' => 'audios',
            'label' => 'Audios',
            'type' => 'dropzone',
            'view_namespace' => 'dropzone::fields',
            'allow_multiple' => true,
            // https://github.com/jargoud/laravel-backpack-dropzone

            'config' => [
                // any option from the Javascript library
                // https://github.com/dropzone/dropzone/blob/main/src/options.js
                'chunkSize' => 1024 * 1024 * 2,
                // for 2 MB
                'chunking' => true,
                'acceptedFiles' => '.mp3,.mpeg,.mpg,.mp4,.m4a,.wav,.opus,.flac,.wma,.aac,.ogg,.au',
                'addRemoveLinks'=> true,
                'dictRemoveFileConfirmation'=> '¿Quieres eliminar este archivo?',
                'dictRemoveFile' => 'Eliminar'
            ],
            // 'disk' => 'public',
            /*'type' => 'upload_multple',
            'attributes' => [
                'accept' => ".mp4,audio/*"
            ],
            'withFiles' => [
                'disk' => 'public',
                // the disk where file will be stored
                'path' => 'medios/comunicados/temp',
                // the path inside the disk where file will be stored
            ] */
        ]);

        CRUD::field([
            'name' => 'pdf',
            'label' => 'Pdf',
            'type' => 'upload',
            // 'disk' => 'public',
            'attributes' => [
                'accept' => "application/pdf"
            ],
            'withFiles' => [
                'disk' => 'public',
                // the disk where file will be stored
                'path' => 'uploads/pdf',
                // the path inside the disk where file will be stored
            ]
        ]);

        CRUD::field('visibilidad')->type('visibilidad');



        Comunicado::saved(function ($comunicado) {
            // Aquí puedes escribir tu lógica personalizada
            // que se ejecutará después de crear o actualizar un comunicado.

            if($comunicado->audios) {
                // dd($comunicado);
                if(TESTAR_CONVERTIDOR_AUDIO2) {
                    $p = new ProcesarAudios($comunicado);
                    $p->handle();
                }
                else{
                    dispatch( new ProcesarAudios($comunicado));
                }

            }
        });


    }

    private function mediaFolder()
    {
        $anioActual = date('Y');
        $mesActual = date('m');

        $folder = "/medios/comunicados/$anioActual/$mesActual";

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
                    'name' => 'my_custom_html',
                    'label' => 'Ver en Web',
                    'type' => 'custom_html',
                    'value' => "<a href='/comunicados/$id?borrador' target='_blank'>➡️ Ver Comunicado en el Sitio Web</a>"
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
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
            }
        ]);

        // MAYBE: do stuff after the autosetup


        // or maybe remove a column
        // CRUD::column('text')->remove();
    }

    public function importCreate()
    {
        try {

            $imported = new WordImport();

            $contenido = Comunicado::create([
                "titulo" => "Importado de " . $_FILES['file']['name'] . "_" . substr(str_shuffle('0123456789'), 0, 5),
                "texto" => $imported->content
            ]);

            // Copiaremos las imágenes a la carpeta de destino
            $imagesFolder = "medios/comunicados/_{$contenido->id}";

            // copia las imágenes desde la carpeta temporal al directorio destino
            $imported->copyImagesTo($imagesFolder);

            // reemplazar la ubicación de las imágenes en el texto del comunicado
            $contenido->texto = preg_replace("/\bmedios\//", "$imagesFolder/", $contenido->texto);
            $contenido->texto = preg_replace("/\.\/medios\//", "/almacen/medios/", $contenido->texto);

            $contenido->imagen = preg_replace("/\bmedios\//", "$imagesFolder/", $contenido->imagen);
            $contenido->imagen = preg_replace("/\.\/medios\//", "/almacen/medios/", $contenido->imagen);
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

            $contenido = Comunicado::findOrFail($id);

            $contenido->texto = $imported->content;

            // Copiaremos las imágenes a la carpeta de destino
            $imagesFolder = "medios/comunicados/_{$contenido->id}";

            // reemplazar la ubicación de las imágenes en el texto del comunicado
            $contenido->texto = preg_replace("/\bmedios\//", "$imagesFolder/", $contenido->texto);
            $contenido->texto = preg_replace("/\.\/medios\//", "/almacen/medios/", $contenido->texto);

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
