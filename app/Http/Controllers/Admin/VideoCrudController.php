<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Prologue\Alerts\Facades\Alert;

/**
 * Class VideoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class VideoCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Video::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/video');
        CRUD::setEntityNameStrings('video', 'videos');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        // Establecer orden por defecto por el campo orden
        $this->crud->orderBy('orden', 'ASC');

        $this->crud->addColumn([
            'name'  => 'orden',
            'label' => 'Orden',
            'type'  => 'number',
            'orderable' => true,
            'searchLogic' => false
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

        $this->crud->addColumn([
            'name'  => 'visibilidad',
            'label' => 'Estado',
            'type'  => 'text',
            'value' => function ($entry) {
                return $entry->visibilidad == 'P' ? '✔️ Publicado' : '⚠️ Borrador';
            }
        ]);

        // Añadir botones para mover arriba/abajo
        $this->crud->addButtonFromModelFunction('line', 'move_up', 'getMoveUpButton', 'beginning');
        $this->crud->addButtonFromModelFunction('line', 'move_down', 'getMoveDownButton', 'beginning');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $this->crud->setValidation([
            'titulo' => 'required|min:2',
            'slug' => [ 'nullable', 'regex:/^[a-z0-9\-]+$/', \Illuminate\Validation\Rule::unique('videos', 'slug')->ignore($this->crud->getCurrentEntryId()) ],
            'orden' => 'nullable|integer|min:0'
        ]);
        // CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

         CRUD::field('titulo')->type('text');

         CRUD::field('slug')->type('text')->hint('Puedes dejarlo en blanco');;

         CRUD::field('descripcion')->type('textarea')->attributes(['maxlength'=>400]);

         CRUD::field('enlace')->type('text')->hint('Enlace al vídeo de youtube.');

         CRUD::field('orden')->type('number')->hint('Número para ordenar los videos. Valores más bajos aparecen primero.');

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

    /**
     * Mover video hacia arriba en el orden
     */
    public function moveUp($id)
    {
        $video = $this->crud->getModel()::findOrFail($id);

        $previousVideo = $this->crud->getModel()::where('orden', '<', $video->orden)
            ->orderBy('orden', 'desc')
            ->first();

        if ($previousVideo) {
            // Intercambiar los órdenes
            $tempOrder = $video->orden;
            $video->orden = $previousVideo->orden;
            $previousVideo->orden = $tempOrder;

            $video->saveQuietly();
            $previousVideo->saveQuietly();

            Alert::success('Video movido hacia arriba correctamente.')->flash();
        }

        return redirect()->back();
    }

    /**
     * Mover video hacia abajo en el orden
     */
    public function moveDown($id)
    {
        $video = $this->crud->getModel()::findOrFail($id);

        $nextVideo = $this->crud->getModel()::where('orden', '>', $video->orden)
            ->orderBy('orden', 'asc')
            ->first();

        if ($nextVideo) {
            // Intercambiar los órdenes
            $tempOrder = $video->orden;
            $video->orden = $nextVideo->orden;
            $nextVideo->orden = $tempOrder;

            $video->saveQuietly();
            $nextVideo->saveQuietly();

            Alert::success('Video movido hacia abajo correctamente.')->flash();
        }

        return redirect()->back();
    }
}
