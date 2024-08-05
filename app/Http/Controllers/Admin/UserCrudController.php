<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\PermissionManager\app\Http\Requests\UserStoreCrudRequest as StoreRequest;
use Backpack\PermissionManager\app\Http\Requests\UserUpdateCrudRequest as UpdateRequest;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\CambioPassword;

/**
 * Class UserCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class UserCrudController extends CrudController
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
        CRUD::setModel(\App\Models\User::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/user');
        CRUD::setEntityNameStrings('usuario', 'usuarios');
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {
        //  CRUD::setFromDb(); // set columns from db columns.

        CRUD::column('id')->type('number');

        CRUD::column('name')->label('Nombre');

        $this->crud->addColumn([
            'name' => 'updated_at',
            'label' => 'Modificado',
            'type' => 'datetime', // Puedes usar 'datetime' o 'date' según el formato que desees mostrar
        ]);

        $this->crud->addColumn(
            [ // n-n relationship (with pivot table)
                'label'     => trans('backpack::permissionmanager.roles'), // Table column heading
                'type'      => 'select_multiple',
                'name'      => 'roles', // the method that defines the relationship in your Model
                'entity'    => 'roles', // the method that defines the relationship in your Model
                'attribute' => 'name', // foreign key attribute that is shown to user
                'model'     => config('permission.models.role'), // foreign key model
            ]
        );

        // CRUD::column('frase')->type('check');
        CRUD::column('email_verified_at')->type('check')->label('@Verificado');
        CRUD::column('profile_photo_path')->type('image')->label('Imagen');
        /**
         * Columns can be defined using the fluent syntax:
         * - CRUD::column('price')->type('number');
         */

         CRUD::addButtonFromView('line', 'generate_password', 'generate_password', 'end');

        CRUD::setOperationSetting('lineButtonsAsDropdown', true);
    }



    public function password($user_id)
    {
        CRUD::hasAccessOrFail('user');

        $user = User::finrOrFail($user_id);

        $words = array(
            "amor", "mente", "observar", "trascendente", "unidad", "cambio", "divulgar", "armonizar", "equilibrio", "muul", "baksaj", "diversidad", "celeste", "kundalini", "grupal", "cielo",
            "ritmo", "equidad", "infinito", "trinidad", "estrella", "plasma", "salud", "ong", "mundo", "utg", "universidad", "sandalia", "baston", "protege", "manto",
            "movimiento", "claridad", "humildad", "hermandad", "confianza", "camino", "predica", "corazon", "estelar", "cayado", "baculo", "ancestral", "libertad", "libre",
            "uno", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve", "diez", "once", "doce", "trece", "intruso", "dispersion", "cyborg", "crea", "crear", "voluntario", "forzado",
            "auto", "autoctono", "oriundo", "primigenio", "aguila", "holograma", "ilusion", "fantasia", "apego", "desapego", "sombra", "sombras", "piensa",
            "pancreas", "pan",  "vino", "sangre", "tierra", "linfatico", "reconocer", "cristo", "cosmico", "interior", "proteccion", "alcanzar", "tutelar", "replica", "replicas", "realidad", "mundos",
            "h1", "h2", "h3", "aium", "rasbek", "shilcars", "melcor", "orjain", "noiwanak", "jalied", "melinus", "mo", "rhaum", "seiph", "orsil", "aumnor", "leer", "asumir", "vaciar",
            "odres", "fractales", "mezclar", "lodo", "agua", "limpiar", "ejemplo", "peques", "sanar", "agregado", "transformar", "transformarse", "cambiar",
            "monje", "pensamiento", "espejo", "testo", "transmutar", "luz", "rompui", "om", "pedir", "neent", "aum", "retro", "retroalimenta", "sinhio", "paraguas", "protector", "cafe",
            "prometeo", "fractal", "xendra", "orbe", "esfera", "arte", "ciencia", "espiritual", "espiritualidad", "ondulatorio", "terapia", "retiro", "guerrero", "prior",
            "norte", "este", "oeste", "sur", "",
            "trascendente", "abiotica", "norte", "oscuridad", "entropia", "feliz", "romper", "beh", "sayab", "tseek", "suut", "kat", "oksah", "ich", "grihal"
        );

        $index = mt_rand(0, count($words) - 1);

        $password = $words[$index] . '.' . mt_rand(1000, 9999);
        \Log::info("nueva contraseña para {$user->name}: $password");

        $user->update(['password' => bcrypt($password)]);

        $user->notify(new CambioPassword($user, $password));

        return response()->json(['user' => $user->name, 'password' => $password]);
    }


    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        /*CRUD::setValidation([
            'name' => 'required|min:2',
            'password' =>'required|min:6'
        ]);*/
        // CRUD::setFromDb(); // set fields from db columns.

        $this->addUserFields();
        $this->crud->setValidation(StoreRequest::class);

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        /*\App\Models\User::creating(function ($entry) {
            $entry->password = \Hash::make($entry->password);
        });*/
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        /*CRUD::setValidation([
            'name' => 'required|min:2',
        ]);*/
        //CRUD::setFromDb(); // set fields from db columns.

        //CRUD::field('password')->hint('Escribe una contraseña solo si deseas cambiarla.');

        $this->addUserFields();
        $this->crud->setValidation(UpdateRequest::class);
        //dd($this->crud->entry);

        // CRUD::field('profile_photo_url')->type('text');

        /**
         * Fields can be defined using the fluent syntax:
         * - CRUD::field('price')->type('number');
         */

        /*
         \App\Models\User::updating(function ($entry) {
            if (request('password') == null) {
                $entry->password = $entry->getOriginal('password');
            } else {
                $entry->password = Hash::make(request('password'));
            }
        });*/
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        $r = $this->traitStore();

        $this->actualizarGrupos($this->crud->entry->id, $this->crud->getRequest()->GruposJSON);

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
        $this->crud->setRequest($this->handlePasswordInput($this->crud->getRequest()));
        $this->crud->unsetValidation(); // validation has already been run

        $r = $this->traitUpdate();

        $this->actualizarGrupos($this->crud->entry->id, $this->crud->getRequest()->GruposJSON);

        return $r;
    }

    protected function actualizarGrupos($user_id, $grupos)
    {
        // dd($grupos);

        $user = User::findOrFail($user_id);

        // Convertir la cadena en un arreglo de IDs
        $gruposUser = array_column(json_decode($grupos, true), 'value');

        // Obtener los grupos actuales del usuario
        $gruposActuales = $user->grupos->pluck('id')->toArray();

        // Comparar los grupos actuales con los nuevos grupos
        $gruposEliminar = array_diff($gruposActuales, $gruposUser);
        $gruposInsertar = array_diff($gruposUser, $gruposActuales);

        // Eliminar los grupos que ya no pertenecen al usuario
        $user->grupos()->detach($gruposEliminar);

        // Insertar las nuevas relaciones de usuario y grupo
        $user->grupos()->syncWithoutDetaching($gruposInsertar);
    }

    /**
     * Handle password input fields.
     */
    protected function handlePasswordInput($request)
    {
        // Remove fields not present on the user.
        $request->request->remove('password_confirmation');
        $request->request->remove('roles_show');
        $request->request->remove('permissions_show');

        // Encrypt password if specified.
        if ($request->input('password')) {
            $request->request->set('password', Hash::make($request->input('password')));
        } else {
            $request->request->remove('password');
        }

        return $request;
    }

    protected function addUserFields()
    {
        $this->crud->addFields([
            [
                'name'  => 'name',
                'label' => trans('backpack::permissionmanager.name'),
                'type'  => 'text',
            ],
            [
                'name'  => 'email',
                'label' => trans('backpack::permissionmanager.email'),
                'type'  => 'email',
            ],
            [
                'name'  => 'password',
                'label' => trans('backpack::permissionmanager.password'),
                'type'  => 'password',
            ],
            [
                'name'  => 'password_confirmation',
                'label' => trans('backpack::permissionmanager.password_confirmation'),
                'type'  => 'password',
            ],
            [
                'name'  => 'profile_photo_path',
                'label' => 'Imagen',
                'type'  => 'image_cover',
                'attributes' => ['folder' => 'profile-photos'],
            ],
            [
                'name'  => 'frase',
                'label' => 'Frase',
                'type'  => 'text',
            ],
            [
                'name'              => 'GruposJSON',
                'label'             => 'Grupos',
                // 'labelOption'       => 'nombre',
                'type'              => 'select_model',
                'model' => 'grupo',
                'options' => null,
                'multiple' => true
            ],
            [
                'name' => 'contacto',
                'label' => 'Contacto',
                'allows_null' => true,
                'type' => 'select',
                'attribute'    => 'nombre',
                'model'       => 'App\Models\Contacto',
                'wrapper'   => [
                    'class'      => 'form-group col-md-4'
                ]
            ],
            [
                // two interconnected entities
                'label'             => trans('backpack::permissionmanager.user_role_permission'),
                'field_unique_name' => 'user_role_permission',
                'type'              => 'checklist_dependency',
                'name'              => 'roles,permissions',
                'subfields'         => [
                    'primary' => [
                        'label'            => trans('backpack::permissionmanager.roles'),
                        'name'             => 'roles', // the method that defines the relationship in your Model
                        'entity'           => 'roles', // the method that defines the relationship in your Model
                        'entity_secondary' => 'permissions', // the method that defines the relationship in your Model
                        'attribute'        => 'name', // foreign key attribute that is shown to user
                        'model'            => config('permission.models.role'), // foreign key model
                        'pivot'            => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns'   => 3, //can be 1,2,3,4,6
                    ],
                    'secondary' => [
                        'label'          => mb_ucfirst(trans('backpack::permissionmanager.permission_plural')),
                        'name'           => 'permissions', // the method that defines the relationship in your Model
                        'entity'         => 'permissions', // the method that defines the relationship in your Model
                        'entity_primary' => 'roles', // the method that defines the relationship in your Model
                        'attribute'      => 'name', // foreign key attribute that is shown to user
                        'model'          => config('permission.models.permission'), // foreign key model
                        'pivot'          => true, // on create&update, do you need to add/delete pivot table entries?]
                        'number_columns' => 3, //can be 1,2,3,4,6
                    ],
                ],
            ]
        ]);
    }



    /* protected function setupShowOperation()
    {
       CRUD::setFromDb(); // set fields from db columns.
       CRUD::column('email_verified_at')->type('text');
       CRUD::column('profile_photo_url')->type('image');
    } */


    public function show($id)
    {
        return redirect("/usuarios/$id");
    }
}
