<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use App\Services\WordImport;
use App\Models\Informe;
use App\Jobs\ProcesarAudios;
use Illuminate\Support\Facades\Log;
use App\Models\Equipo;
// use Backpack\CRUD\app\Library\Validation\Rules\ValidUploadMultiple;

// esto permite testar la conversión de audio al guardar el comunicado
define('TESTAR_CONVERTIDOR_AUDIO3', false);

/**
 * Class InformeCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InformeCrudController extends CrudController
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
        CRUD::setModel(Informe::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/informe');
        CRUD::setEntityNameStrings('informe', 'informes');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // si no tiene permisos de "administrar equipos" entonces es un simple coordinador de un equipo (o varios tal vez)
        // añadimos un filtro para mostrar solo los informes del equipo del coordinador

        if (!backpack_user()->can('administrar equipos')) {
            CRUD::addClause("whereIn", "equipo_id", backpack_user()->equiposQueCoordina->pluck('id')->toArray());
        }


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
            'name' => 'equipoNombre',
            'label' => 'Equipo',
            'type' => 'text'
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
            'slug' => [ \Illuminate\Validation\Rule::unique('informes', 'slug')->ignore($this->crud->getCurrentEntryId()) ],
            'descripcion' => 'max:400',
            // 'audios' => ValidUploadMultiple::field()->file('max:20000'),
        ]);

        CRUD::setFromDb(); // set fields from db columns.


        CRUD::field([
            // select_from_array
            'name' => 'categoria',
            'label' => "Categoría",
            'type' => 'select_from_array',
            'options' => ['Informe' => 'Informe', 'Orden del día' => 'Orden del día', 'Acta' => 'Acta', 'Anexo' => 'Anexo', 'Acuerdo' => 'Acuerdo', 'Otros'=>'Otros'],
            'allows_null' => false,
            'default' => 'General',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper' => [
                'class' => 'form-group col-md-3'
            ],
        ])->after('titulo');


        if (backpack_user()->can('administrar equipos'))
        CRUD::field('equipo_id')->type('select')->after('titulo')->wrapper(['class' => 'form-group col-md-3']);
        else {
            // obtenemos de la URL si existe el parametro equipo_id
            $equipo_id = request()->get('equipo_id');
            if ($equipo_id) {
                $equipo = Equipo::findOrFail($equipo_id);
                // comprobamos si el usuario actual es coordinador de este equipo
                if(!backpack_user()->equiposQueCoordina->pluck('id')->contains($equipo_id)){
                    // denegamos el acceso
                    abort(403);
                }
                CRUD::field('equipo_nombre_mostrar')->type('text')->label('Equipo')->value($equipo->nombre)->after('titulo')->attributes(['readonly' => 'readonly']);
                CRUD::field('equipo_id')->type('hidden')->value($equipo_id);
            }
            else{
                $equipos = backpack_user()->equiposQueCoordina;
                CRUD::field([
                    // select_from_array
                    'name' => 'equipo_id',
                    'label' => "Equipo",
                    'type' => 'select_from_array',
                    'options' => array_combine($equipos->pluck( 'id')->toArray(),$equipos->pluck('nombre')->toArray()),
                    'allows_null' => false,
                    'wrapper' => [
                        'class' => 'form-group col-md-3'
                    ],
                ])->after('titulo');
            }
    }

         $folder = $this->getMediaFolder();

        CRUD::field('descripcion')->type('textarea')->label("Descripción corta (opcional)");

        CRUD::field('texto')->type('tiptap_editor')->attributes(['folder' => $folder]);

        CRUD::field([
            'name' => 'audios',
            'label' => 'Audios',
            'type' => 'dropzone',
            'view_namespace' => 'dropzone::fields',
            'allow_multiple' => true,
            // https://github.com/jargoud/laravel-backpack-dropzone
            'hint'=>'Audios de voz (se va a reducir su calidad para ahorrar espacio)',

            'config' => [
                // any option from the Javascript library
                // https://github.com/dropzone/dropzone/blob/main/src/options.js
                'chunkSize' => 1024 * 1024 * 2,
                // for 2 MB
                'chunking' => true,
                'acceptedFiles' => 'audio/*',
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


        /* CRUD::field('audios')->type('upload_multiple')->attributes(['accept'=>'audio/*'])->withFiles([
            'disk' => 'public',
            'path' => 'uploads'
        ]);
        CRUD::field('archivos')->type('upload_multiple')->withFiles([
            'disk' => 'public',
            'path' => 'uploads',
    ]); */


            CRUD::field([
            'name' => 'archivos',
            'label' => 'Archivos adjuntos',
            'type' => 'dropzone',
            'view_namespace' => 'dropzone::fields',
            'allow_multiple' => true,
            // https://github.com/jargoud/laravel-backpack-dropzone
            'hint'=>'Documentos, pdf, word, powerpoint, openoffice, vídeos cortos. Se pueden poner audios mp3 aquí si se quiere conservar su calidad ',
            'config' => [
                // any option from the Javascript library
                // https://github.com/dropzone/dropzone/blob/main/src/options.js
                'chunkSize' => 1024 * 1024 * 2,
                // for 2 MB
                'chunking' => true,
                'acceptedFiles' => '.pdf,.doc,.docx,.odt,.rtf,.txt,.xls,.xlsx,.ods,.csv,.mp3,.mp4,.zip', // mp4 para vídeo
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
        ])->after('audios');

        // CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

        CRUD::field('visibilidad')->type('visibilidad');


        Informe::saved(function ($informe) use ($folder) {
            // Aquí puedes escribir tu lógica personalizada
            // que se ejecutará después de crear o actualizar un informe.

            Log::info("informe::saved");

            // AUDIOS
            if($informe->audios) {
                $año = $informe->created_at->year;
                $carpetaAudios = "/almacen/medios/informes/audios/{$informe->equipo->slug}/$año/{$informe->id}";
                Log::info("informe::saved - audios carpeta " . $carpetaAudios);
                if(TESTAR_CONVERTIDOR_AUDIO3) {
                    $p = new ProcesarAudios($informe, $carpetaAudios);
                    $p->handle();
                }
                else{
                    dispatch( new ProcesarAudios($informe, $carpetaAudios));
                }
            }

            // ARCHIVOS
            // throw new \Exception("hola esto es un error");
                if($informe->archivos) {
                    $carpetaArchivos = "$folder/archivos";
                    Log::info("informe::saved - archivos carpeta " . $carpetaArchivos);
                    if($informe->guardarArchivos($carpetaArchivos))
                        $informe->saveQuietly();
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
        // si no tiene permisos de "administrar equipos" entonces es un simple coordinador de un equipo (o varios tal vez)
        // añadimos un control para asegurarnos que no puede editar un informe que de alguno de sus equipos

        if (!backpack_user()->can('administrar equipos')) {
            $informe = $this->crud->getCurrentEntry();
            if($informe && !backpack_user()->equiposQueCoordina->contains('id', $informe->equipo_id)) {
                $this->crud->denyAccess(['update']);
            }
        }


        $this->setupCreateOperation();
    }


    public function show($id)
    {
        $informe = Informe::find($id);
        return $informe->visibilidad == 'P' ? redirect("/informes/$id") : redirect("/informes/$id?borrador");
    }


    public function importCreate()
    {
        $contenido = Informe::create([
            "titulo" => "Importado de " . $_FILES['file']['name'] . "_" . substr(str_shuffle('0123456789'), 0, 5),
            "texto" => ""
        ]);

        return $this->importUpdate($contenido->id);
    }


    public function importUpdate($id)
    {
        $contenido = Informe::findOrFail($id);

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
