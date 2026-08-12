<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StoreComunicadoInteriorizacionRequest;
use App\Jobs\ProcesarAudios;
use App\Models\ComunicadoInteriorizacion;
use App\Services\WordImport;
use App\Traits\CrudContenido;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\ReviseOperation\ReviseOperation;

class ComunicadoInteriorizacionCrudController extends CrudController
{
    use CreateOperation;
    use CrudContenido;
    use DeleteOperation;
    use ListOperation;
    use ReviseOperation;
    use ShowOperation;
    use UpdateOperation;

    public function setup()
    {
        CRUD::setModel(ComunicadoInteriorizacion::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/comunicado-interiorizacion');
        CRUD::setEntityNameStrings('comunicado de interiorización', 'comunicados de interiorización');
    }

    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'id',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'titulo',
            'label' => 'Título',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'nivel',
            'label' => 'Nivel',
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->nivel == 1 ? 'Nivel 1' : 'Nivel 2';
            },
        ]);

        $this->crud->addColumn([
            'name' => 'ciclo',
            'label' => 'Ciclo',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'numero',
            'label' => 'Número',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'fecha_comunicado',
            'label' => 'Fecha',
            'type' => 'date',
        ]);

        $this->crud->addColumn([
            'name' => 'visibilidad',
            'label' => 'Estado',
            'type' => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
            },
        ]);

        CRUD::setOperationSetting('lineButtonsAsDropdown', true);

        CRUD::addButtonFromView('top', 'import_create', 'import_create', 'end');
        CRUD::addButtonFromView('line', 'import_update', 'import_update', 'beginning');
    }

    protected function setupCreateOperation()
    {
        $this->crud->setValidation(StoreComunicadoInteriorizacionRequest::class);

        $folder = $this->getMediaFolder();

        $this->crud->addField([
            'name' => 'titulo',
            'label' => 'Título',
            'type' => 'text',
            'wrapper' => ['class' => 'form-group col-md-8'],
        ]);

        $this->crud->addField([
            'name' => 'slug',
            'type' => 'text',
            'label' => 'Slug',
            'value' => '',
            'wrapper' => ['class' => 'form-group col-md-4'],
            'hint' => 'Se genera automáticamente si se deja vacío.',
        ]);

        $this->crud->addField([
            'name' => 'nivel',
            'label' => 'Nivel',
            'type' => 'select_from_array',
            'options' => ['1' => 'Nivel 1', '2' => 'Nivel 2'],
            'allows_null' => false,
            'wrapper' => ['class' => 'form-group col-md-2'],
        ]);

        $this->crud->addField([
            'name' => 'ciclo',
            'label' => 'Ciclo',
            'type' => 'text',
            'value' => '',
            'wrapper' => ['class' => 'form-group col-md-3'],
        ]);

        $this->crud->addField([
            'name' => 'numero',
            'label' => 'Número',
            'type' => 'text',
            'value' => '',
            'wrapper' => ['class' => 'form-group col-md-2'],
        ]);

        $this->crud->addField([
            'name' => 'fecha_comunicado',
            'label' => 'Fecha comunicado',
            'type' => 'date',
            'wrapper' => ['class' => 'form-group col-md-3'],
        ]);

        $this->crud->addField([
            'name' => 'descripcion',
            'label' => 'Descripción',
            'type' => 'textarea',
            'value' => '',
            'attributes' => ['maxlength' => 400, 'required' => true],
        ]);

        $this->crud->addField([
            'name' => 'texto',
            'label' => 'Texto',
            'type' => 'tiptap_editor',
            'attributes' => ['folder' => $folder],
        ]);

        $this->crud->addField([
            'name' => 'imagen',
            'label' => 'Imagen',
            'type' => 'image_cover',
            'attributes' => ['folder' => $folder, 'from' => 'texto'],
        ]);

        $this->crud->addField([
            'name' => 'ano',
            'type' => 'hidden',
        ]);

        $this->crud->addField([
            'name' => 'audios',
            'label' => 'Audios',
            'type' => 'dropzone',
            'view_namespace' => 'dropzone::fields',
            'allow_multiple' => true,
            'config' => [
                'chunkSize' => 1024 * 1024 * 2,
                'chunking' => true,
                'acceptedFiles' => '.mp3,.mpeg,.mpg,.mp4,.m4a,.wav,.opus,.flac,.wma,.aac,.ogg,.au',
                'addRemoveLinks' => true,
                'dictRemoveFileConfirmation' => '¿Quieres eliminar este archivo?',
                'dictRemoveFile' => 'Eliminar',
            ],
        ]);

        $this->crud->addField([
            'name' => 'visibilidad',
            'type' => 'visibilidad',
        ]);

        ComunicadoInteriorizacion::saved(function ($comunicado) {
            if ($comunicado->audios) {
                $ano = date('Y', strtotime($comunicado->fecha_comunicado));
                $folder = "/almacen/medios/comunicados_interiorizacion/audios/$ano";

                dispatch(new ProcesarAudios(ComunicadoInteriorizacion::class, $comunicado->id, $folder))
                    ->onQueue('audio_processing');
            }
        });
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    public function show($id)
    {
        $comunicado = ComunicadoInteriorizacion::find($id);

        return $comunicado->visibilidad == 'P'
            ? redirect("/comunicados-interiorizacion/$comunicado->slug")
            : redirect("/comunicados-interiorizacion/$comunicado->slug?borrador");
    }

    public function importCreate()
    {
        $contenido = ComunicadoInteriorizacion::create([
            'titulo' => 'Importado de '.$_FILES['file']['name'].'_'.substr(str_shuffle('0123456789'), 0, 5),
            'texto' => '',
            'ano' => date('Y'),
            'nivel' => 1,
            'ciclo' => date('Y'),
            'fecha_comunicado' => date('Y-m-d'),
            'visibilidad' => 'B',
        ]);

        return $this->importUpdate($contenido->id);
    }

    public function importUpdate($id)
    {
        $contenido = ComunicadoInteriorizacion::findOrFail($id);

        try {
            $imported = new WordImport;

            $imported->copyImagesTo($this->getMediaFolder($contenido), true);

            $contenido->texto = $imported->content;

            if (! $contenido->imagen || $contenido->imagen == '/almacen/medios/logos/sello_tseyor_64.png') {
                $guias = ['Shilcars', 'Rasbek', 'Melcor', 'Noiwanak', 'Aumnor', 'Aium Om', 'Orjaín', 'Mo', 'Rhaum', 'Jalied'];
                $regex = "/\b(".implode('|', $guias).")\b/";
                if (preg_match($regex, $contenido->texto, $matches)) {
                    $guia = strtolower(str_replace(['í', ' '], ['i', ''], $matches[0]));
                    $contenido->imagen = "/almacen/medios/guias/$guia.jpg";
                }
            }

            $contenido->texto = preg_replace("#(.*\!\[\]\(/almacen/medios/logos/sello_tseyor_64[^)]+\))(\**Universidad Tseyor de Granada)#", "$1\n\n$2", $contenido->texto);

            $contenido->save();

            return response()->json([
                'id' => $contenido->id,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
