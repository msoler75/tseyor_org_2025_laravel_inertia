<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use App\Services\WordImport;
use App\Models\Comunicado;
use App\Http\Requests\StoreComunicadoRequest;
use App\Jobs\ProcesarAudios;
use Illuminate\Support\Facades\Log;

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
    use \Backpack\ReviseOperation\ReviseOperation;
    use \App\Traits\CrudContenido;

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
            'titulo' => 'required|min:7|max:255',
            'texto' => 'required',
            'numero' => 'required|numeric|min:1|max:9999',
            'categoria' => 'required',
            'fecha_comunicado' => 'required',
            'descripcion' => 'max:400',
            'audios' => [
                'array',
            ],
             // 'audios.*' => [                new DropzoneRule("audios", ['audio']), ],
            // 'pdf' => 'max:20000|mimes:pdf',
        ]);
        */

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

        $folder = $this->getMediaFolder();

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength' => 400]);

        // CRUD::field('texto')->type('text_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('texto')->type('tiptap_editor')->attributes(['folder' => $folder]);

        CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

        CRUD::field('ano')->type('hidden');

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
                'addRemoveLinks' => true,
                'dictRemoveFileConfirmation' => '¿Quieres eliminar este archivo?',
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

        // if(false)
        /*CRUD::field([
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
                'path' => "$folder/pdf",
                // the path inside the disk where file will be stored
            ]
        ]);*/

        CRUD::field('visibilidad')->type('visibilidad');


        Comunicado::saving(function ($comunicado) {
            $año = date('Y', strtotime($comunicado->fecha_comunicado));
            $comunicado->ano = $año;
        });


        Comunicado::saved(function ($comunicado) {
            // Aquí puedes escribir tu lógica personalizada
            // que se ejecutará después de crear o actualizar un comunicado.

            if ($comunicado->audios) {
                // dd($comunicado);

                $año = date('Y', strtotime($comunicado->fecha_comunicado));
                $folder = "/almacen/medios/comunicados/audios/$año";

                if (TESTAR_CONVERTIDOR_AUDIO2) {
                    $p = new ProcesarAudios(Comunicado::class, $comunicado->id, $folder);
                    $p->handle();
                } else {
                    dispatch(new ProcesarAudios(Comunicado::class, $comunicado->id, $folder));
                }

            }
        });

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
        $comunicado = Comunicado::find($id);
        return $comunicado->visibilidad == 'P' ? redirect("/comunicados/$id") : redirect("/comunicados/$id?borrador");
    }

    // show whatever you want
    /* protected function setupShowOperation2()
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
    */

    public function importCreate()
    {
        $contenido = Comunicado::create([
            "titulo" => "Importado de " . $_FILES['file']['name'] . "_" . substr(str_shuffle('0123456789'), 0, 5),
            "texto" => "",
            "ano" => date('Y')
        ]);

        return $this->importUpdate($contenido->id);
    }


    public function importUpdate($id)
    {
        $contenido = Comunicado::findOrFail($id);

        try {
            // inicializa el importador en base a $_FILES
            $imported = new WordImport();

            // copia las imágenes desde la carpeta temporal al directorio destino, sobreescribiendo las anteriores en la carpeta
            $imported->copyImagesTo($this->getMediaFolder($contenido), true);

            // ahora las imagenes están con la nueva ubicación
            $contenido->texto = $imported->content;

            if (!$contenido->imagen || $contenido->imagen == "/almacen/medios/logos/sello_tseyor_64.png") {
                $guias = ['Shilcars', 'Rasbek', 'Melcor', 'Noiwanak', 'Aumnor', 'Aium Om', 'Orjaín', 'Mo', 'Rhaum', 'Jalied'];
                $regex = "/\b(" . implode("|", $guias) . ")\b/";
                if (preg_match($regex, $contenido->texto, $matches)) {
                    // Log::info("guia encontrado:" . print_r($matches, true));
                    $guia =  strtolower(str_replace(["í", " "], ["i", ""], $matches[0]));
                    $contenido->imagen = "/almacen/medios/guias/$guia.jpg";
                }
            }

            // corregimos un caso particular de formato del logo de Tseyor
            $contenido->texto = preg_replace("#(.*\!\[\]\(/almacen/medios/logos/sello_tseyor_64[^)]+\))(\**Universidad Tseyor de Granada)#", "$1\n\n$2", $contenido->texto);

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
