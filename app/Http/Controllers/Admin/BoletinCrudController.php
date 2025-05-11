<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Boletin;

class BoletinCrudController extends CrudController
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
        CRUD::setModel(Boletin::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/boletin');
        CRUD::setEntityNameStrings('boletín', 'boletines');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @return void
     */
    protected function setupListOperation()
    {
        $this->crud->addColumn([
            'name' => 'id',
            'label' => 'ID',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'titulo',
            'label' => 'Título',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'tipo',
            'label' => 'Tipo',
            'type' => 'text',
        ]);

        $this->crud->addColumn([
            'name' => 'anyo',
            'label' => 'Año',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'mes',
            'label' => 'Mes',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'semana',
            'label' => 'Semana',
            'type' => 'number',
        ]);

        $this->crud->addColumn([
            'name' => 'enviado',
            'label' => 'Enviado',
            'type' => 'boolean',
        ]);

        CRUD::addButtonFromView('line', 'enviar_boletin', 'enviar_boletin', 'beginning');
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @return void
     */
    protected function setupCreateOperation()
    {
        CRUD::setValidation([ // Puedes agregar reglas de validación aquí
            'titulo' => 'required|string|max:255',
            'tipo' => 'required|string',
            'texto' => 'required|string',
            'anyo' => 'required|integer',
            'mes' => 'required|integer',
            'semana' => 'required|integer',
        ]);

        CRUD::field('titulo');


        CRUD::addField([   // select_from_array
            'name'        => 'tipo',
            'label'       => 'Tipo',
            'type'        => 'select_from_array',
            'options'     => Boletin::TIPOS,
            'allows_null' => false,
            'default'     => 'bisemanal',
            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);


        CRUD::field('texto')->type('tiptap_editor');
        CRUD::field('anyo');
        CRUD::field('mes');
        CRUD::field('semana');
        CRUD::field('enviado')->type('boolean');
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Método para enviar un boletín.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enviarBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);
        $boletin->enviarBoletin();

        return redirect()->back()->with('success', 'Boletín enviado correctamente.');
    }

    public function show($id)
    {
        return redirect("/boletines/$id");
    }
}
