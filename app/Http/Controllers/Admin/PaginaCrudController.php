<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PaginaRequest;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use App\Models\Pagina;

/**
 * Class PaginaCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class PaginaCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Pagina::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/pagina');
        CRUD::setEntityNameStrings('página', 'páginas');
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


   //add div row using 'div' widget and make other widgets inside it to be in a row
   Widget::add()->to('before_content')->type('div')->class('row')->content([

    //widget made using fluent syntax
    Widget::make()
        ->type('card')
        ->class('card bg-dark text-white mb-1') // optional
        ->content([
            'body'=>'Estas páginas solo se cargan si no están ya predefinidas, y en cualquier sirven para el SEO y para indexar el buscador.'
        ])


]);

        $this->crud->addColumn([
            'name'  => 'titulo',
            'label' => 'Título',
            'type'  => 'text'
        ]);

        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime', // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);

        $this->crud->addColumn([
            'name' => 'ruta',
            'label' => 'Ruta',
            'type' => 'text'
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
        CRUD::setValidation([
            'titulo' => 'required|min:3',
            'descripcion' => 'max:400',
            // 'texto' => 'required',
            'ruta' => 'max:255',
            'ruta_regreso' => 'max:255',
            'regreso_nombre' => 'max:64'
        ]);

        CRUD::setFromDb(); // set fields from db columns.

        CRUD::field('descripcion')->type('textarea')->hint('Descripción corta para el SEO.');

        $folder = "/medios/paginas";

        // CRUD::field('texto')->type('text_tinymce')->attributes(['folder' => $folder]);

        CRUD::field('texto')->type('text_tinymce')->attributes(['folder' => $folder])->hint('Contenido de la página.');

        CRUD::field('palabras_clave')->type('textarea')->hint('Poner solo el texto o palabras clave para indexar esta página en el buscador global.');

        CRUD::field('visibilidad')->type('visibilidad');

        CRUD::field('atras_ruta')->label('Atrás Ruta')->type('text')->after('ruta')->hint('url de regreso');

        CRUD::field('atras_texto')->label('Atrás Texto')->type('text')->after('atras_ruta')->hint('texto en la url de regreso');
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
        $pagina = Pagina::findOrFail($id);

        return $pagina->visibilidad != 'P'? redirect("/{$pagina->ruta}?borrador") : redirect("/{$pagina->ruta}");
    }
}
