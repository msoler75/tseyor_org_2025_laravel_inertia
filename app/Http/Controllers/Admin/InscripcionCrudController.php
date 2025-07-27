<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Backpack\CRUD\app\Library\Widget;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Notifications\InscripcionesAsignadas;

/**
 * Class InscripcionCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class InscripcionCrudController extends CrudController
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
        CRUD::setModel(\App\Models\Inscripcion::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/inscripcion');
        CRUD::setEntityNameStrings('inscripción', 'inscripciones');

        // Configurar permisos
        $this->crud->denyAccess(['create']); // No permitir crear desde Backpack, se usa el formulario público

        // Los botones personalizados se agregan via widgets para evitar errores de vista
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operation-list-entries
     * @return void
     */
    protected function setupListOperation()
    {

        // Widget de estadísticas - usando vista personalizada para mejor control del layout
        Widget::add()
            ->to('before_content')
            ->type('view')
            ->view('custom.widgets.inscripciones_estadisticas');

        // Widget de acciones masivas
        Widget::add()
            ->to('before_content')
            ->type('view')
            ->view('custom.widgets.inscripciones_acciones_masivas');

        // Widget de filtros básicos (compatibles con versión gratuita)
        Widget::add()
            ->to('before_content')
            ->type('view')
            ->view('custom.widgets.inscripciones_filtros_basicos');

        // Aplicar filtros básicos desde parámetros GET
        $this->aplicarFiltrosBasicos();

        // Columnas básicas (sin filtros PRO)
        CRUD::column('id')->type('number')->label('ID')->orderable(true);

        CRUD::column('created_at')->type('closure')->label('Fecha Inscripción')
            ->function(function ($entry) {
                $dateTime = Carbon::parse($entry->created_at);
                return '<span title="' . $dateTime->format('d/m/Y H:i') . '">' .
                       $dateTime->diffForHumans() . '</span>';
            })->escaped(false);

        CRUD::column('estado')->type('closure')->label('Estado')
            ->function(function ($entry) {
                $estados = config('inscripciones.estados');
                $colores = [
                    'nueva' => 'bg-warning',
                    'rebotada' => 'bg-warning',
                    'asignada' => 'bg-danger',
                    'contactado' => 'bg-info',
                    'encurso' => 'bg-info',
                    'finalizado' => 'bg-success',
                    'nocontesta' => 'bg-secondary',
                    'duplicada' => 'bg-dark',
                    'nointeresado' => 'bg-dark'
                ];
                $estado = $entry->estado;
                $colorClass = $colores[$estado] ?? 'bg-secondary';
                if ($estado === 'nueva') {
                    $texto = 'Nueva';
                } elseif (isset($estados[$estado]['etiqueta']) && !empty($estados[$estado]['etiqueta'])) {
                    $texto = $estados[$estado]['etiqueta'];
                } elseif (!empty($estado)) {
                    $texto = $estado;
                } else {
                    $texto = 'Sin estado';
                }
                return '<span class="badge ' . $colorClass . '">' . $texto . '</span>';
            })->escaped(false);

        CRUD::column('nombre')->type('text')->label('Nombre')->orderable(true);

        CRUD::column('email')->type('email')->label('Email')->orderable(true);

        CRUD::column('usuario_asignado')->type('closure')->label('Tutor Asignado')
            ->function(function ($entry) {
                if ($entry->usuarioAsignado) {
                    return '<i class="la la-user"></i> ' . $entry->usuarioAsignado->name;
                }
                return '<span class="text-muted">Sin asignar</span>';
            })->escaped(false)->orderable(false)->searchable(false);

        CRUD::column('pais')->type('text')->label('País')->orderable(true);

        CRUD::column('region')->type('text')->label('Región');

        CRUD::column('ciudad')->type('text')->label('Ciudad');

        CRUD::column('comentario')->type('text')->label('Comentario del inscrito');

        CRUD::column('notas')->type('text')->label('Notas');

        CRUD::column('fecha_asignacion')->type('closure')->label('Fecha Asignación')
            ->function(function ($entry) {
                if ($entry->fecha_asignacion) {
                    $fecha = Carbon::parse($entry->fecha_asignacion);
                    return '<span title="' . $fecha->format('d/m/Y H:i') . '">' .
                           $fecha->diffForHumans() . '</span>';
                }
                return '<span class="text-muted">-</span>';
            })->escaped(false);

        CRUD::column('ultima_notificacion')->type('closure')->label('Última Notificación')
            ->function(function ($entry) {
                if ($entry->ultima_notificacion) {
                    $fecha = Carbon::parse($entry->ultima_notificacion);
                    $dias = $fecha->diffInDays(now());
                    $class = $dias > 7 ? 'text-danger' : ($dias > 3 ? 'text-warning' : 'text-success');

                    return '<span class="' . $class . '" title="' . $fecha->format('d/m/Y H:i') . '">' .
                           $fecha->diffForHumans() . '</span>';
                }
                return '<span class="text-muted">-</span>';
            })->escaped(false);

        // Columna de acciones personalizadas
        /*CRUD::column('acciones')->type('closure')->label('Gestionar')
            ->function(function ($entry) {
                $url = route('inscripciones.mis-asignaciones', $entry->id);
                return '<a href="' . $url . '" class="btn btn-sm btn-outline-primary" target="_blank" title="Gestionar Inscripción">
                    <i class="la la-cogs"></i> Gestionar
                </a>';
            })->escaped(false)->orderable(false)->searchable(false);
            */

        // Configuraciones adicionales
        CRUD::setOperationSetting('lineButtonsAsDropdown', true);
        CRUD::orderBy('created_at', 'desc');
        CRUD::setDefaultPageLength(25);

        // Habilitar checkboxes para selección múltiple
        CRUD::enableBulkActions();

        // Configurar atributos personalizados para las filas
        CRUD::setOperationSetting('tableRowAttributes', function($entry) {
            return [
                'data-entry-id' => $entry->id,
                'data-id' => $entry->id
            ];
        });
    }    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        // Pestaña 1: Datos del inscrito
        CRUD::field('nombre')->type('text')->label('Nombre Completo')->validationRules('required|max:255')->tab('Datos del inscrito');
        CRUD::field('email')->type('email')->label('Email')->validationRules('required|email|max:255')->tab('Datos del inscrito');
        CRUD::field('telefono')->type('text')->label('Teléfono')->validationRules('nullable|max:50')->tab('Datos del inscrito');
        CRUD::field('fecha_nacimiento')->type('date')->label('Fecha de Nacimiento')->validationRules('nullable|date')->tab('Datos del inscrito');
        CRUD::field('ciudad')->type('text')->label('Ciudad')->validationRules('nullable|max:255')->tab('Datos del inscrito');
        CRUD::field('region')->type('text')->label('Región/Estado')->validationRules('nullable|max:255')->tab('Datos del inscrito');
        CRUD::field('pais')->type('text')->label('País')->validationRules('nullable|max:255')->tab('Datos del inscrito');
        CRUD::field('comentario')->type('textarea')->label('Comentario Inicial')->validationRules('nullable|max:1000')->tab('Datos del inscrito');

        // Pestaña 2: Gestión de la inscripción


        $this->crud->addField([
            'name'              => 'user_id',
            'label'             => 'Tutor Asignado',
            'type'              => 'select_model',
            'model'             => 'user',
            'options'           => null,
            'multiple'          => false,
            'allows_null'       => true,
            'placeholder'       => 'Buscar tutor...',
            'wrapper'           => ['class' => 'form-group col-md-6'],
            'hint'              => 'Escribe para buscar usuarios por nombre. En esta lista solo aparecen algunos.',
            'tab'               => 'Gestión de la inscripción',
        ]);

             CRUD::field('estado')->type('select_from_array')
            ->options(collect(config('inscripciones.estados'))->mapWithKeys(function ($item, $key) {
                return [$key => $item['etiqueta']];
            })->toArray())
            ->default('nueva')
            ->label('Estado')
            ->validationRules('required')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->tab('Gestión de la inscripción');

        CRUD::field('user_id')->validationRules('nullable|exists:users,id')->tab('Gestión de la inscripción');
        CRUD::field('fecha_asignacion')->type('datetime')
            ->label('Fecha de Asignación')
            ->validationRules('nullable|date')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->tab('Gestión de la inscripción');
        CRUD::field('ultima_notificacion')->type('datetime')
            ->label('Última Notificación')
            ->validationRules('nullable|date')
            ->wrapper(['class' => 'form-group col-md-6'])
            ->tab('Gestión de la inscripción');
        CRUD::field('notas')->type('textarea')
            ->label('Notas de Seguimiento')
            ->validationRules('nullable')
            ->attributes(['rows' => 6])
            ->tab('Gestión de la inscripción');
        // Campo legacy para compatibilidad
        CRUD::field('asignado')->type('text')
            ->label('Asignado (Campo obsoleto, se mantiene por compatibilidad)')
            ->hint('Campo mantenido por compatibilidad. Usar "Tutor Asignado" preferentemente.')
            ->validationRules('nullable|max:256')
            ->tab('Gestión de la inscripción');
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
     * Define what happens when the Show operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operation-show
     * @return void
     */
    protected function setupShowOperation()
    {
        // Widget de información de seguimiento
        Widget::add()
            ->to('before_content')
            ->type('view')
            ->view('custom.widgets.inscripcion_seguimiento');

        // Información básica
        CRUD::column('id')->type('number')->label('ID');
        CRUD::column('created_at')->type('datetime')->label('Fecha de Inscripción');
        CRUD::column('updated_at')->type('datetime')->label('Última Actualización');

        // Datos personales
        CRUD::column('nombre')->type('text')->label('Nombre Completo');
        CRUD::column('email')->type('email')->label('Email');
        CRUD::column('telefono')->type('text')->label('Teléfono');
        CRUD::column('fecha_nacimiento')->type('date')->label('Fecha de Nacimiento');

        // Ubicación
        CRUD::column('ciudad')->type('text')->label('Ciudad');
        CRUD::column('region')->type('text')->label('Región');
        CRUD::column('pais')->type('text')->label('País');

        // Comentario inicial
        CRUD::column('comentario')->type('textarea')->label('Comentario Inicial');

        // Estado y asignación
        CRUD::column('estado')->type('closure')->label('Estado')
            ->function(function ($entry) {
                $estados = config('inscripciones.estados');
                return $estados[$entry->estado]['etiqueta'] ?? $entry->estado;
            });

        CRUD::column('usuario_asignado')->type('closure')->label('Tutor Asignado')
            ->function(function ($entry) {
                if ($entry->usuarioAsignado) {
                    return '<i class="la la-user"></i> ' . $entry->usuarioAsignado->name;
                }
                return '<span class="text-muted">Sin asignar</span>';
            })->escaped(false);

        CRUD::column('fecha_asignacion')->type('datetime')->label('Fecha de Asignación');
        CRUD::column('ultima_notificacion')->type('datetime')->label('Última Notificación');

        // Notas de seguimiento
        CRUD::column('notas')->type('textarea')->label('Notas de Seguimiento');

        // Campo legacy
        CRUD::column('asignado')->type('text')->label('Asignado (Legacy)');
    }

    /**
     * Store a newly created resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $request = $this->crud->getRequest();

        // Si se asigna tutor y el estado es 'nueva' o 'asignada', forzar estado 'asignada' y fecha de asignación
        if (!empty($request->user_id) && in_array($request->estado, ['nueva', 'asignada'])) {
            $request->merge(['estado' => 'asignada']);
            $request->merge(['fecha_asignacion' => now()]);
        }

        $this->crud->unsetValidation();
        return $this->traitStore();
    }

    /**
     * Update the specified resource in the database.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update()
    {
        $this->crud->setRequest($this->crud->validateRequest());
        $request = $this->crud->getRequest();
        $currentEntry = $this->crud->getCurrentEntry();

        // Si se asigna tutor y el estado es 'nueva' o 'asignada', usar asignarA para notificar
        if (!empty($request->user_id) && in_array($request->estado, ['nueva', 'asignada'])) {
            $usuario = User::find($request->user_id);
            if ($usuario && $currentEntry->user_id != $usuario->id) {
                $currentEntry->asignarA($usuario, 'Asignación desde Secretaría de Tseyor');
                // Actualizar otros campos editados
                $currentEntry->fill($request->except(['user_id', 'estado', 'fecha_asignacion']));
                $currentEntry->save();
                $this->crud->unsetValidation();
                return redirect()->back()->with('success', 'Inscripción actualizada y tutor notificado.');
            }
        }

        $this->crud->unsetValidation();
        return $this->traitUpdate();
    }



    /**
     * Método personalizado para asignar masivamente inscripciones
     */
    public function asignarMasiva(Request $request)
    {
        Log::info('Asignación masiva iniciada', [
            'inscripcion_ids' => $request->input('inscripcion_ids'),
            'user_id' => $request->input('user_id')
        ]);

        $inscripcionIds = $request->input('inscripcion_ids', []);
        $userId = $request->input('user_id');

        if (empty($inscripcionIds) || !$userId) {
            Log::warning('Asignación masiva falló: parámetros vacíos', [
                'inscripcion_ids' => $inscripcionIds,
                'user_id' => $userId
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Selecciona inscripciones y un tutor'
            ]);
        }

        $user = User::find($userId);
        if (!$user) {
            Log::warning('Asignación masiva falló: usuario no encontrado', ['user_id' => $userId]);
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }

        $count = 0;
        $errores = [];
        $procesadas = [];
        $inscripcionesNotificar = [];

        foreach ($inscripcionIds as $id) {
            $inscripcion = \App\Models\Inscripcion::find($id);

            if (!$inscripcion) {
                $errores[] = "Inscripción {$id} no encontrada";
                Log::warning("Inscripción no encontrada", ['id' => $id]);
                continue;
            }

            $estadoAnterior = $inscripcion->estado;
            $tutorAnterior = $inscripcion->user_id;

            try {
                $inscripcion->update([
                    'user_id' => $userId,
                    'estado' => 'asignada',
                    'fecha_asignacion' => now(),
                    'asignado' => $user->name // Por compatibilidad
                ]);

                $count++;
                $procesadas[] = [
                    'id' => $id,
                    'estado_anterior' => $estadoAnterior,
                    'tutor_anterior' => $tutorAnterior,
                    'nuevo_tutor' => $userId
                ];

                // Para notificación masiva
                $inscripcionesNotificar[] = [
                    'id' => $inscripcion->id,
                    'nombre' => $inscripcion->nombre,
                    'email' => $inscripcion->email,
                    'telefono' => $inscripcion->telefono,
                    'ciudad' => $inscripcion->ciudad,
                    'comentario' => $inscripcion->comentario
                ];

                Log::info("Inscripción {$id} asignada correctamente", [
                    'estado_anterior' => $estadoAnterior,
                    'tutor_anterior' => $tutorAnterior,
                    'nuevo_tutor' => $userId
                ]);

            } catch (\Exception $e) {
                $errores[] = "Error al asignar inscripción {$id}: " . $e->getMessage();
                Log::error("Error al asignar inscripción {$id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // Notificar al tutor si hay inscripciones asignadas
        if (count($inscripcionesNotificar) > 0) {
            try {
                $user->notify(new InscripcionesAsignadas($inscripcionesNotificar));
                // Actualizar ultima_notificacion de cada inscripción notificada
                foreach ($inscripcionIds as $id) {
                    $inscripcion = \App\Models\Inscripcion::find($id);
                    if ($inscripcion) {
                        $inscripcion->ultima_notificacion = now();
                        $inscripcion->save();
                    }
                }
            } catch (\Exception $e) {
                Log::error('Error enviando notificación masiva a tutor: ' . $e->getMessage());
            }
        }

        Log::info('Asignación masiva completada', [
            'total_procesadas' => $count,
            'total_errores' => count($errores),
            'procesadas' => $procesadas,
            'errores' => $errores
        ]);

        $message = "Se asignaron {$count} inscripciones a {$user->name}";
        if (count($errores) > 0) {
            $message .= ". Errores: " . implode(', ', $errores);
        }

        return response()->json([
            'success' => $count > 0,
            'message' => $message,
            'debug' => [
                'procesadas' => $count,
                'errores' => count($errores),
                'detalles' => $procesadas
            ]
        ]);
    }

    /**
     * Método para cambiar estado rápidamente
     */
    public function cambiarEstado(Request $request, $id)
    {
        $inscripcion = \App\Models\Inscripcion::findOrFail($id);
        $nuevoEstado = $request->input('estado');
        $comentario = $request->input('comentario', '');

        if (!array_key_exists($nuevoEstado, config('inscripciones.estados'))) {
            return response()->json([
                'success' => false,
                'message' => 'Estado no válido'
            ]);
        }

        $inscripcion->actualizarEstado($nuevoEstado, $comentario);

        return response()->json([
            'success' => true,
            'message' => 'Estado actualizado correctamente'
        ]);
    }

    /**
     * Aplicar filtros básicos (compatibles con versión gratuita)
     */
    private function aplicarFiltrosBasicos()
    {
        $request = request();

        // Filtro por estado
        if ($request->filled('estado')) {
            $estado = $request->input('estado');
            $this->crud->addClause('where', 'estado', $estado);
            // Debugging: descomentar la siguiente línea si necesitas verificar el filtro
            // \Log::info('Filtrando por estado: ' . $estado);
        }

        // Filtro por tutor
        if ($request->filled('tutor')) {
            $this->crud->addClause('where', 'user_id', $request->input('tutor'));
        }

        // Búsqueda por nombre o email
        if ($request->filled('buscar')) {
            $buscar = $request->input('buscar');
            $this->crud->addClause('where', function ($query) use ($buscar) {
                $query->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('email', 'like', "%{$buscar}%");
            });
        }
    }
}
