<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use App\Services\WordImport;
use App\Models\Informe;
use App\Jobs\ProcesarAudios;

// esto permite testar la conversión de audio al guardar el comunicado
define('TESTAR_CONVERTIDOR_AUDIO', false);

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
            'descripcion' => 'max:400',
        ]);

        CRUD::setFromDb(); // set fields from db columns.


        CRUD::field([
            // select_from_array
            'name' => 'categoria',
            'label' => "Categoría",
            'type' => 'select_from_array',
            'options' => ['General' => 'General', 'OD' => 'Orden del día', 'Acta' => 'Acta', 'Anexo' => 'Anexo', 'Acuerdo' => 'Acuerdo'],
            'allows_null' => false,
            'default' => 'General',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper' => [
                'class' => 'form-group col-md-3'
            ],
        ])->after('titulo');


        CRUD::field('equipo_id')->type('select')->after('titulo')->wrapper(['class' => 'form-group col-md-3']);

        $folder = $this->mediaFolder();

        CRUD::field('descripcion')->type('textarea');

        CRUD::field('texto')->type('text_tinymce')->attributes(['folder' => $folder]);

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


        CRUD::field([
            'name' => 'archivos',
            'label' => 'Archivos adjuntos',
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
                'acceptedFiles' => '.mp4,.pdf,.doc,.docx,.odt,.rtf,.txt,.xls,.xlsx,.ods,.csv',
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

        // CRUD::field('imagen')->type('image_cover')->attributes(['folder' => $folder, 'from' => 'texto']);

        CRUD::field('visibilidad')->type('visibilidad');

        Informe::saved(function ($informe) {
            // Aquí puedes escribir tu lógica personalizada
            // que se ejecutará después de crear o actualizar un informe.

            if(!$informe->archivos) return;

            $month = $informe->created_at->month;
            $month = $month < 10 ? "0{$month}" : $month;
            $carpetaArchivos = "medios/informes/{$informe->created_at->year}/$month/{$informe->id}/archivos";

            $pathDestino = Storage::disk('public')->path($carpetaArchivos);

            $archivosNuevo = [];

            $cambiado = false;
            foreach ($informe->archivos as $idx => $archivoActual) {
                if (strpos($archivoActual, $carpetaArchivos) === FALSE) {
                    // hay que copiar el archivo a la nueva ubicación
                    if (!Storage::disk('public')->exists($pathDestino)) {
                        Storage::disk('public')->makeDirectory($pathDestino, 0755, true, true);
                    }
                    $archivoDestino = $carpetaArchivos . '/' . basename($archivoActual);
                    Storage::disk('public')->move($archivoActual, $archivoDestino);
                    $archivosNuevo[] = $archivoDestino;
                    $cambiado = true;
                }
                else {
                    $archivosNuevo[] = $archivoActual;
                }
            }

            if ($cambiado)
                $informe->update(['archivos'=>$archivosNuevo]);
        });
    }




    private function mediaFolder()
    {
        $anioActual = date('Y');
        $mesActual = date('m');

        $folder = "/medios/informes/$anioActual/$mesActual";

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
        $informe = \App\Models\Informe::find($id);
        return $informe->visibilidad == 'P' ? redirect("/informes/$id") : redirect("/informes/$id?borrador");
    }


    public function importCreate()
    {
        try {

            $imported = new WordImport();

            $contenido = Informe::create([
                "titulo" => "Importado de " . $_FILES['file']['name'] . "_" . substr(str_shuffle('0123456789'), 0, 5),
                "texto" => $imported->content
            ]);

            // Copiaremos las imágenes a la carpeta de destino
            $imagesFolder = "medios/informe/_{$contenido->id}";

            // copia las imágenes desde la carpeta temporal al directorio destino
            $imported->copyImagesTo($imagesFolder);

            // reemplazar la ubicación de las imágenes en el texto del comunicado
            $contenido->texto = preg_replace("/\bmedia\//", "$imagesFolder/", $contenido->texto);
            $contenido->texto = preg_replace("/\.\/medios\//", "/almacen/medios/", $contenido->texto);

            //$contenido->imagen = preg_replace("/\bmedia\//", "$imagesFolder/", $contenido->imagen);
            //$contenido->imagen = preg_replace("/\.\/medios\//", "/almacen/medios/", $contenido->imagen);
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

            $contenido = Informe::findOrFail($id);

            $contenido->texto = $imported->content;

            // Copiaremos las imágenes a la carpeta de destino
            $imagesFolder = "medios/informes/_{$contenido->id}";

            // reemplazar la ubicación de las imágenes en el texto del comunicado
            $contenido->texto = preg_replace("/\bmedia\//", "$imagesFolder/", $contenido->texto);
            $contenido->texto = preg_replace("/\.\/medios\//", "/almacen/medios/", $contenido->texto);

            $contenido->descripcion = null; // para que se regenere

            // $contenido->imagen = null; // para que se elija otra nueva, si la hay
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
