<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Validation\Rules\ValidUpload;
use Illuminate\Validation\Rule;
use App\Models\Audio;

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
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        // CRUD::setValidation(AudioRequest::class);
        CRUD::setFromDb(); // set fields from db columns.

        $rules = [
            'titulo' => 'required|min:8',
            'slug' => [
                Rule::unique('audios', 'slug')->ignore(request()->id),
            ],
            'audio' => 'nullable|file|mimes:mp3',
            'enlace' => 'nullable|url',
        ];

        // Añadir regla personalizada para validar que al menos uno de los campos mp3 o enlace esté presente
        $this->crud->addField([
            'name' => 'mp3_or_enlace',
            'type' => 'custom_html',
            'value' => '<input type="hidden" name="mp3_or_enlace" value="hh">',
        ]);

        $rules['mp3_or_enlace'] = [
            'required',
            function ($attribute, $value, $fail) {
                $mp3 = request()->file('audio');
                $enlace = request()->input('enlace');
                $existingMp3 = $this->crud->getCurrentEntry() ? $this->crud->getCurrentEntry()->audio : null;
                \Log::info("input mp3:", [$mp3, $existingMp3]);
                if (!$mp3 && !$enlace && !$existingMp3) {
                    $fail('Se requiere proporcionar un archivo MP3 o un enlace.');
                }
            },
        ];

        CRUD::setValidation($rules);
        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        // donde se guardan los archivos de audio
        $folder = "medios/audios"; // así funciona con upload y disco 'public'

        CRUD::field('slug')->type('text')->hint('Puedes dejarlo en blanco');

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength'=>400]);

        CRUD::field('enlace')->type('text')->hint('Solo si es un audio externo, poner la url aquí. En tal caso no debe subirse el archivo audio en el campo anterior.');

        CRUD::field('audio')->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => $folder, // the path inside the disk where file will be stored
            ])
            ->attributes(['accept' => ".mp3"]);

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
                              'Otros'=>'Otros'],
            'allows_null' => false,
            'default'     => 'Meditaciones',
            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        CRUD::field('visibilidad')->type('visibilidad');
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
