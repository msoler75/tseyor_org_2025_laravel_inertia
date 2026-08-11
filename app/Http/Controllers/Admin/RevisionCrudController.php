<?php

namespace App\Http\Controllers\Admin;

use App\Models\Revision;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
use Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanel;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;

/**
 * Class RevisionCrudController
 *
 * @property-read CrudPanel $crud
 */
class RevisionCrudController extends CrudController
{
    use ListOperation;
    use ShowOperation;

    /**
     * Configure the CrudPanel object. Apply settings to all operations.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(Revision::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/revision');
        CRUD::setEntityNameStrings('revision', 'revisiones');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     *
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'tituloContenido',
            'label' => 'Contenido',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'coleccion',
            'label' => 'Colección',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'operacion',
            'label' => 'Operación',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'autor',
            'label' => 'Autor',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'created_at',
            'label' => 'Fecha',
            'type' => 'datetime',
        ]);

        $colecciones = Revision::select('revisionable_type')
            ->distinct()
            ->pluck('revisionable_type')
            ->mapWithKeys(function ($type) {
                $coleccion = str_replace('App\\Models\\', '', $type);
                $coleccion = strtolower($coleccion);
                $coleccion .= substr($coleccion, -1) == 'n' ? 'es' : 's';

                return [$type => ucfirst($coleccion)];
            })
            ->toArray();

        Widget::add([
            'name' => 'revision_collection_filter',
            'type' => 'revision_collection_filter',
            'viewNamespace' => 'vendor.backpack.crud.filters',
            'section' => 'before_content',
            'colecciones' => $colecciones,
        ]);

        if (request()->filled('revisionable_type')) {
            $this->crud->addClause('where', 'revisionable_type', request('revisionable_type'));
        }
    }

    public function show($id)
    {
        $revision = Revision::findOrFail($id);

        return redirect($revision->revisionUrl);
    }
}
