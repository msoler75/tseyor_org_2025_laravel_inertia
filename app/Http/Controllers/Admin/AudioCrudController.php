<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Validation\Rules\ValidUpload;
use Illuminate\Validation\Rule;
use App\Http\Requests\StoreAudioRequest;
use App\Models\Audio;
use App\Pigmalion\StorageItem;

/**
 * Class AudioCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AudioCrudController extends CrudController
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
        CRUD::setModel(Audio::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/audio');
        CRUD::setEntityNameStrings('audio', 'audio');
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
            'name'  => 'titulo',
            'label' => 'Título',
            'type'  => 'text'
        ]);

        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime',
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

        $this->crud->addColumn([
            'name' => 'audio_play',
            'label' => 'Audio',
            'type' => 'view',
            'view' => 'vendor.backpack.crud.columns.audio_play'
        ]);


        CRUD::setOperationSetting('lineButtonsAsDropdown', true);
        CRUD::addButtonFromView('top', 'audio_player_component', 'audio_player_component', 'beginning');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation(StoreAudioRequest::class);

        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        // donde se guardan los archivos de audio
        // $folder = "medios/audios"; // así funciona con upload y disco 'public'
        $folder = $this->getMediaFolder();

        CRUD::field('slug')->type('text')->hint('Puedes dejarlo en blanco');

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength'=>400]);

        CRUD::field('enlace')->type('text')->hint('Solo si es un audio externo, poner la url aquí. En tal caso no debe subirse el archivo audio en el campo anterior.');


        // truco para el campo 'upload' de backpack
        $loc = new StorageItem($folder);
        $relativeFolder = $loc->relativeLocation;

        CRUD::field('audio')->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => $relativeFolder, // the path inside the disk where file will be stored
            ])
            ->attributes(['accept' => ".mp3"])
            ->allowMediaLibraryDeletion(true);

        CRUD::addField([   // select_from_array
            'name'        => 'categoria',
            'label'       => 'Categoría',
            'type'        => 'select_from_array',
            'options'     => ['Meditaciones'=>'Meditaciones',
                              'Talleres'=>'Talleres',
                              'Cuentos'=>'Cuentos',
                              'Reflexiones'=>'Reflexiones',
                              'Música clásica'=>'Música clásica',
                              'Rayos de luz'=>'Rayos de luz',
                              'Minicápsulas de Aium Om'=>'Minicápsulas de Aium Om',
                              'Guías Estelares'=>'Guías Estelares',
                              'Canciones'=>'Canciones',
                              'Espacio para la interiorización'=>'Espacio para la interiorización',
                              'Mensajes al corazón'=>'Mensajes al corazón',
                              'Otros'=>'Otros'],
            'allows_null' => false,
            'default'     => 'Meditaciones',
            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        CRUD::field('visibilidad')->type('visibilidad');

        Audio::saved(function ($audio) {
            //dd($audio);

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
        $audio = Audio::find($id);
        return $audio->visibilidad == 'P' ? redirect("/audios/$id") : redirect("/audios/$id?borrador");
    }
}
