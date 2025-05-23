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
/*
        $this->crud->addColumn([
            'name' => 'semana',
            'label' => 'Semana',
            'type' => 'number',
        ]);*/

        $this->crud->addColumns([
            [
                'name' => 'numeroSuscriptores',
                'label' => 'Suscritos',
                'type' => 'number',
            ],
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


        CRUD::addField([   // select_from_array
            'name'        => 'tipo',
            'label'       => 'Tipo',
            'type'        => 'select_from_array',
            'options'     => Boletin::TIPOS,
            'allows_null' => false,
            'default'     => 'mensual',
            'wrapper'   => [
                'class'      => 'form-group col-md-3'
            ],
        ]);

        // Inyectar el token de boletín como campo oculto
        CRUD::addField([
            'name' => 'boletin_token',
            'type' => 'hidden',
            'value' => config('app.boletin.token'),
        ]);

        CRUD::field('generar')->type('generar_contenido_boletin');

        CRUD::field('titulo');


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

    public function show($id)
    {
        return redirect("/boletines/$id");
    }

    /**
     * Envío inmediato del boletín
     * @param mixed $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function enviarBoletin($id)
    {
        $boletin = Boletin::findOrFail($id);
        if($boletin->enviado) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede enviar el boletín porque ya estaba marcado como enviado.'
            ], 400);
        }

        $boletin->enviarBoletin();

        // return redirect()->back()->with('success', 'Boletín enviado correctamente.');
        return response()->json([
            'success' => true,
            'message' => 'Boletín enviado correctamente.'
        ], 200);
    }


}
