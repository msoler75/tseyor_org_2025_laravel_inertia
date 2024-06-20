<?php

namespace App\Http\Controllers\Admin;

use App\Models\Equipo;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

/**
 * Class EquipoCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class EquipoCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation {
        store as traitStore;
    }
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation {
        update as traitUpdate;
    }
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
        CRUD::setModel(Equipo::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/equipo');
        CRUD::setEntityNameStrings('equipo', 'equipos');
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
            'type' => 'datetime',
        ]);


        $this->crud->addColumn([
            'name' => 'imagen',
            'label' => 'Imagen',
            'type' => 'image',
            'value' => function ($entry) {
                return $entry->imagen . '?mh=25';
            }
        ]);


        $this->crud->addColumn([
            'label' => 'miembros',
            'value' => function ($entry) {
                return $entry->miembros()->count();
            }
        ]);

        CRUD::column('CreadorNombre')->type('text')->label("Creado por");
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
            'slug' => [ \Illuminate\Validation\Rule::unique('equipos', 'slug')->ignore($this->crud->getCurrentEntryId()) ],
            'descripcion' => 'required|max:400',
            'anuncio' => 'max:400',
            'informacion' => 'max:400',
            'reuniones' => 'max:400',
        ]);

        // CRUD::setFromDb(); // set fields from db columns.

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        // CRUD::field('user')->type('select');


        CRUD::field('nombre')->type('text');

        CRUD::field('slug')->type('text')->hint('Puedes dejarlo en blanco');

        CRUD::field('descripcion')->type('textarea')->attributes(['maxlength'=>400]);

        CRUD::field('imagen')->type('image_cover');

        CRUD::field('categoria')->type('text');

        CRUD::field('anuncio')->type('tiptap_editor_simple');

        CRUD::field('reuniones')->type('tiptap_editor_simple');

        CRUD::field('informacion')->type('tiptap_editor_simple');

        CRUD::field('grupo')->type('hidden');

        $this->crud->addField([
            'name'              => 'CoordinadoresJSON',
            'label'             => 'Coordinadores',
            'type'              => 'select_model',
            'model' => 'user',
            'options' => null,
            'multiple' => true,
        ]);

        $this->crud->addField([
            'name'              => 'MiembrosJSON',
            'label'             => 'Miembros',
            'type'              => 'select_model',
            'model' => 'user',
            'options' => null,
            'multiple' => true,
            'hint' => 'Opcionalmente puedes poner aquí también a los coordinadores, aunque no es necesario.'
        ]);

        CRUD::field('ocultarMiembros')->type('checkbox')->label('Ocultar solicitudes de ingreso');
        CRUD::field('ocultarCarpetas')->type('checkbox')->label('Ocultar carpetas');
        CRUD::field('ocultarArchivos')->type('checkbox')->label('Ocultar archivos');
        CRUD::field('ocultarMiembros')->type('checkbox')->label('Ocultar miembros');
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
        return redirect("/equipos/$id");
    }



    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run

        $r = $this->traitStore();

        $this->actualizarMiembros($this->crud->entry->id, $this->crud->getRequest()->CoordinadoresJSON,  $this->crud->getRequest()->MiembrosJSON);

        return $r;
    }


    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->unsetValidation(); // validation has already been run

        $r = $this->traitUpdate();

        $this->actualizarMiembros($this->crud->entry->id, $this->crud->getRequest()->CoordinadoresJSON,  $this->crud->getRequest()->MiembrosJSON);

        return $r;
    }


    /**
     * Actualiza los miembros del equipo y sus roles
     */
    protected function actualizarMiembros($equipo_id, $coordinadores, $miembros)
    {
        $equipo = Equipo::findOrFail($equipo_id);

        // Convertir la cadena en un arreglo de IDs
        $coordinadores_ids = array_column(json_decode($coordinadores, true), 'value');
        $miembros_ids = array_column(json_decode($miembros, true), 'value');

        // Obtenemos los miembros actuales con sus roles
        $miembros_actuales = $equipo->miembros()->get();

        // Iterar sobre las membresías existentes y actualizar los roles o eliminarlas si es necesario
        foreach ($miembros_actuales as $membresia) {
            $user_id = $membresia->pivot->user_id;

            if (in_array($user_id, $coordinadores_ids)) {
                // Si el usuario es un coordinador, actualizar el rol en la membresía existente
                $equipo->miembros()->updateExistingPivot($user_id, ['rol' => 'coordinador']);
            } else {
                // Si el usuario es un miembro normal, actualizar el rol en la membresía existente
                $equipo->miembros()->updateExistingPivot($user_id, ['rol' => 'miembro']);
            }
        }

        // Obtener los miembros que deben ser removidos
        $miembros_a_remover = $miembros_actuales->filter(function ($miembro) use ($coordinadores_ids, $miembros_ids) {
            return !in_array($miembro->id, $coordinadores_ids) && !in_array($miembro->id, $miembros_ids);
        });

        // Remover las membresías que ya no pertenecen al equipo ni son coordinadores
        $equipo->miembros()->detach($miembros_a_remover->pluck('id'));

        // Obtener los nuevos miembros que deben ser agregados
        $nuevos_miembros_ids = array_merge($coordinadores_ids, $miembros_ids);
        $nuevos_miembros_ids_a_agregar = array_diff($nuevos_miembros_ids, $miembros_actuales->pluck('id')->toArray());

        // Agregar solo los nuevos miembros al equipo
        $miembros_nuevos = [];
        foreach ($nuevos_miembros_ids_a_agregar as $miembro_id) {
            if (in_array($miembro_id, $coordinadores_ids)) {
                $miembros_nuevos[$miembro_id] = ['rol' => 'coordinador'];
            } else {
                $miembros_nuevos[$miembro_id] = ['rol' => 'miembro'];
            }
        }

        $equipo->miembros()->attach($miembros_nuevos);
        $equipo->miembros()->updateExistingPivot($coordinadores_ids, ['rol' => 'coordinador']);
    }
}
