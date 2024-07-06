<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Centro;
use App\Pigmalion\Countries;

/**
 * Class CentroCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class CentroCrudController extends CrudController
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
        CRUD::setModel(Centro::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/centro');
        CRUD::setEntityNameStrings('centro', 'centros');
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
            'name'  => 'nombre',
            'label' => 'Nombre',
            'type'  => 'text'
        ]);


        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime', // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);


        $this->crud->addColumn([
            'name'  => 'imagen',
            'label' => 'Imagen',
            'type'  => 'image'
        ]);


        $this->crud->addColumn([
            'name'  => 'nombrePais',
            'label' => 'País',
            'type'  => 'text',
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
        CRUD::setValidation([
            'nombre' => 'required|min:8',
            'slug' => [\Illuminate\Validation\Rule::unique('centros', 'slug')->ignore($this->crud->getCurrentEntryId())],
            'descripcion' => 'required|max:400',
            'imagen' => 'required|max:255',
            'pais' => 'required',
            'provincia' => 'max:64',
            'poblacion'=>'required|max:128',
        ]);

        // CRUD::setFromDb(); // set fields from db columns.

        CRUD::field('nombre')->type('text')->attributes(['maxlength' => 256, 'required' => 'required']);

        CRUD::field('slug')->type('text')->attributes(['maxlength' => 256])->hint('Nombre corto para los enlaces. No lo rellenes si no sabes como funciona');

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength' => 400, 'rows' => 4]);

        $folder = $this->getMediaFolder();

        $galeria = $this->crud->getCurrentEntryId();

        CRUD::field('imagen')->type('image_cover')->label($galeria?'Imágenes del centro':'Imagen principal')->attributes(['folder' => $folder, 'sublabel'=>$galeria?'Galería de imágenes':'', 'can-delete'=>$galeria, 'list-images'=>$galeria]);


        CRUD::field('poblacion')->type('text')->attributes(['maxlength' => 256, 'required' => 'required'])->wrapper([
                'class'      => 'form-group col-md-4'
        ]);

        CRUD::field([   // select_from_array
            'name'        => 'pais',
            'label'       => "País",
            'type'        => 'select_from_array',
            'options'     => Countries::$list,
            'allows_null' => false,
            'default'     => 'ES',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
        ]);


        CRUD::field([
            'name' => 'contacto',
            'label' => 'Contacto',
            'allows_null' => true,
            'type' => 'select',
            'attribute'    => 'nombre',
            'model'       => 'App\Models\Contacto',
            'wrapper'   => [
                'class'      => 'form-group col-md-4'
            ],
            'hint' => 'Seleccionar ficha de contacto (si la hay)'
        ]);

        CRUD::field('entradas')->type('textarea')->hint('slug de entradas de blog, separados por comas o saltos de linea. Ejemplo: un-retiro-espiritual, unas-jornadas-enriquecedoras, etc.')->attributes(['rows' => 4]);

        CRUD::field('libros')->type('textarea')->hint('slug de libros, separados comas o saltos de linea. Ejemplo: los-guias-estelares, el-rayo-sincronizador, ...')->attributes(['rows' => 4]);

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
        return redirect("/centros/$id");
    }
}
