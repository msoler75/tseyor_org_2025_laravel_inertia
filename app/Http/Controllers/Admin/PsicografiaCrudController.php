<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Http\Requests\StorePsicografiaRequest;
use App\Models\Psicografia;
use App\Pigmalion\StorageItem;

/**
 * Class GuiaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PsicografiaCrudController extends CrudController
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
        CRUD::setModel(Psicografia::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/psicografia');
        CRUD::setEntityNameStrings('psicografia', 'psicografias');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //CRUD::setFromDb(); // set columns from db columns.

        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */

        CRUD::column('id');

        CRUD::column('updated_at')->type('datetime')->label('modificado');

        CRUD::column('titulo')->type('text');

        CRUD::column('categoria')->type('text');

        $this->crud->addColumn([
            'name' => 'imagen',
            'label' => 'Miniatura',
            'type' => 'custom_html',
            'value' => function($entry) {
                $src = $entry->imagen;
                if(!$src)
                    return '<span class="text-muted">-</span>';
                $src = (new StorageItem($src))->urlPath;
                $miniatura = $src."?mh=50&mw=50"; // miniatura de la imagen
                $enlace = $src;
                return '<a href="' . $enlace . '" target="_blank">
                            <img src="' . $miniatura . '" style="object-fit: cover;" alt="Miniatura">
                        </a>';
            },
            'escaped' => false
        ]);


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
        $this->crud->setValidation(StorePsicografiaRequest::class);

        CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        CRUD::field('descripcion')->type('textarea');

        CRUD::field([   // select_from_array
            'name'        => 'categoria',
            'label'       => "Categoría",
            'type'        => 'select_from_array',
            'options'     => ['General' => 'General', 'Palabras Maya'=>'Palabras Maya', 'Aniversarios'=>'Aniversarios', 'Neent' => 'Neent', 'Equipos y Departamentos'=>'Equipos y Departamentos','FractalOm' => 'FractalOm', 'Muulasterios y Casas Tseyor' => 'Muulasterios y Casas Tseyor', 'Priores y Belankiles' => 'Priores y Belankiles', 'Contacto con las bases' => 'Contacto con las bases', 'Eventos' => 'Eventos'],
            'allows_null' => false,
            'default'     => 'General',
            // 'allows_multiple' => true, // OPTIONAL; needs you to cast this to array in your model;

            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        $folder = $this->getMediaFolder();

        //         $folder = 'medios/psicografias';
        // truco para el campo 'upload' de backpack
        $loc = new StorageItem($folder);
        $relativeFolder = $loc->relativeLocation;

        CRUD::field('imagen')->type('upload')
            ->withFiles([
                'disk' => 'public', // the disk where file will be stored
                'path' => $relativeFolder, // the path inside the disk where file will be stored
            ])
            ->attributes(['accept' => "image"])
            ->allowMediaLibraryDeletion(true)
            ->wrapper([
                'class'      => 'form-group col-md-6'
            ]);


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
        $psicografia = Psicografia::findOrFail($id);
        return redirect("/psicografias/$id");
    }
}
