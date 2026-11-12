<?php

namespace App\Models;

use App\Notifications\InscripcionAsignada;
use App\Notifications\InscripcionReasignada;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;
use Venturecraft\Revisionable\RevisionableTrait;

class Inscripcion extends Model
{
    use CrudTrait;
    use RevisionableTrait;
    use Searchable;

    protected $table = 'inscripciones';

    protected $fillable = [
        'nombre',
        'fecha_nacimiento',
        'ciudad',
        'region',
        'pais',
        'email',
        'telefono',
        'comentario',
        'estado',
        'asignado', // Campo legacy para compatibilidad
        'user_id',
        'fecha_asignacion',
        'ultima_notificacion',
        'ultima_actividad',
        'notas',
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'ultima_notificacion' => 'datetime',
        'ultima_actividad' => 'datetime',
    ];

    /**
     * Flag interno para evitar recursión cuando el modelo se guarda desde los observers
     */
    protected bool $skipEstadoObserver = false;

    /**
     * Flag interno para evitar recursión cuando cambia el tutor (user_id)
     */
    protected bool $skipUserObserver = false;

    /**
     * Public setter to control skipping the estado observer from controllers
     */
    public function setSkipEstadoObserver(bool $skip): void
    {
        $this->skipEstadoObserver = $skip;
    }

    /**
     * Public setter to control skipping the user_id observer from controllers
     */
    public function setSkipUserObserver(bool $skip): void
    {
        $this->skipUserObserver = $skip;
    }

    /**
     * Flag de instancia para suprimir envío de notificaciones de asignación (uso en wrapper/mass)
     */
    protected bool $suppress_assignment_notifications = false;

    /**
     * Flag global para suprimir notificaciones durante operaciones masivas
     */
    protected static bool $globalSuppressAssignmentNotifications = false;

    /**
     * NOTA: ya no usamos almacenamiento temporal en array; los cambios de estado
     * y de tutor se gestionan directamente en el evento `updating`.
     */

    /**
     * Helper para suprimir notificaciones de asignación durante una operación masiva
     */
    public static function suppressAssignmentNotifications(callable $callback)
    {
        static::$globalSuppressAssignmentNotifications = true;
        try {
            return $callback();
        } finally {
            static::$globalSuppressAssignmentNotifications = false;
        }
    }

    /**
     * Relación con el usuario asignado
     */
    public function usuarioAsignado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Obtiene el nombre del usuario asignado (para compatibilidad)
     */
    public function getNombreUsuarioAsignadoAttribute(): ?string
    {
        if ($this->usuarioAsignado) {
            return $this->usuarioAsignado->name;
        }

        // Fallback a la columna legacy 'asignado' si existe
        return $this->asignado;
    }

    /**
     * Obtiene los estados disponibles desde configuración
     */
    public static function getEstadosDisponibles(): array
    {
        return config('inscripciones.estados');
    }

    /**
     * Obtiene los estados no elegibles desde configuración
     */
    public static function getEstadosNoElegibles(): array
    {
        return config('inscripciones.estados_no_elegibles', []);
    }

    protected static function booted()
    {
        static::updating(function ($model) {
            // Cambio de estado
            if ($model->isDirty('estado') && empty($model->skipEstadoObserver)) {
                $estadoAnterior = $model->getOriginal('estado');
                $estadoNuevo = $model->estado;
                $actor = auth()->user()?->name ?? 'Sistema';

                // Marcar actividad
                $model->ultima_actividad = now();

                // Añadir la nota directamente (se guardará en el mismo proceso de save)
                $fecha = now()->format('d/m/Y H:i');
                $nota = "- {$fecha} - {$actor}: Cambiado de '{$estadoAnterior}' a '{$estadoNuevo}'";
                $model->notas = ($model->notas ? $model->notas."\n" : '').$nota;
            }

            // Cambio de tutor (user_id)
            if ($model->isDirty('user_id') && empty($model->skipUserObserver)) {
                $userAnteriorId = $model->getOriginal('user_id');
                $userNuevoId = $model->user_id;
                $actor = auth()->user()?->name ?? 'Sistema';

                // Marcar actividad
                $model->ultima_actividad = now();

                // Si se está asignando un tutor y el estado original era 'nueva',
                // actualizar el estado a 'asignada' automáticamente (si no se cambió explícitamente el estado).
                $estadoAnterior = $model->getOriginal('estado');
                if ($userNuevoId && $estadoAnterior === 'nueva' && ! $model->isDirty('estado')) {
                    $model->estado = 'asignada';
                    // Añadir nota de cambio de estado implícito
                    $fechaEstado = now()->format('d/m/Y H:i');
                    $notaEstado = "- {$fechaEstado} - {$actor}: Cambiado de 'nueva' a 'asignada'";
                    $model->notas = ($model->notas ? $model->notas."\n" : '').$notaEstado;
                }

                if ($userNuevoId && ! $userAnteriorId) {
                    $nuevoNombre = User::find($userNuevoId)?->name ?? 'Usuario desconocido';
                    $notaUser = '- '.now()->format('d/m/Y H:i')." - {$actor}: Asignado a {$nuevoNombre}";
                    // Registrar fecha de asignación cuando se asigna por primera vez
                    $model->fecha_asignacion = now();
                } elseif ($userNuevoId && $userAnteriorId && $userNuevoId !== $userAnteriorId) {
                    $nombreAnterior = User::find($userAnteriorId)?->name ?? 'Usuario desconocido';
                    $nuevoNombre = User::find($userNuevoId)?->name ?? 'Usuario desconocido';
                    $notaUser = '- '.now()->format('d/m/Y H:i')." - {$actor}: Reasignado de {$nombreAnterior} a {$nuevoNombre}";
                    // Al reasignar, actualizar fecha_asignacion
                    $model->fecha_asignacion = now();
                } elseif (! $userNuevoId) {
                    $nombreAnterior = User::find($userAnteriorId)?->name ?? 'Usuario desconocido';
                    $notaUser = '- '.now()->format('d/m/Y H:i')." - {$actor}: Desasignado (antes {$nombreAnterior})";
                } else {
                    $notaUser = null;
                }

                if (! empty($notaUser)) {
                    $model->notas = ($model->notas ? $model->notas."\n" : '').$notaUser;
                }

                // Enviar notificaciones: nuevo tutor y, si procede, tutor anterior
                if ($userNuevoId
                    && empty($model->suppress_assignment_notifications)
                    && empty(static::$globalSuppressAssignmentNotifications)) {
                    try {
                        $usuario = User::find($userNuevoId);
                        if ($usuario) {
                            $usuario->notify(new InscripcionAsignada($model));
                            // Marcar ultima_notificacion para persistir en el mismo save
                            $model->ultima_notificacion = now();
                        }
                    } catch (\Exception $e) {
                        Log::channel('inscripciones')->error('Error enviando notificación de asignación: '.$e->getMessage());
                    }
                }

                if ($userAnteriorId && $userNuevoId && $userAnteriorId !== $userNuevoId) {
                    try {
                        $usuarioPrevio = User::find($userAnteriorId);
                        $usuarioNuevo = User::find($userNuevoId);
                        if ($usuarioPrevio && $usuarioNuevo) {
                            $usuarioPrevio->notify(new InscripcionReasignada($model, $usuarioNuevo));
                        }
                    } catch (\Exception $e) {
                        Log::channel('inscripciones')->error('Error notificando al tutor anterior sobre reasignación: '.$e->getMessage());
                    }
                }
            }
        });
    }

    /**
     * Verifica si necesita seguimiento automático
     */
    /**
     * Devuelve las inscripciones duplicadas de esta (mismo email o nombre+apellidos,
     * creadas en el mismo mes), excluyendo la propia.
     */
    public function duplicados(): \Illuminate\Support\Collection
    {
        if (! $this->created_at) {
            return collect();
        }

        return self::where('id', '!=', $this->id)
            ->where(function ($q) {
                $q->where('email', $this->email)
                    ->orWhere('nombre', $this->nombre);
            })
            ->whereYear('created_at', $this->created_at->year)
            ->whereMonth('created_at', $this->created_at->month)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Detecta si una inscripción recién creada es duplicada de otra existente
     * (mismo email o nombre+apellidos, mismo mes). Si lo es, la marca como
     * 'duplicada' y acumula los datos de contacto en la inscripción maestra.
     */
    public static function procesarDuplicado(self $nueva): void
    {
        if (! $nueva->created_at) {
            $nueva->created_at = now();
        }

        $candidatos = self::where('id', '!=', $nueva->id)
            ->where(function ($q) use ($nueva) {
                $q->where('email', $nueva->email)
                    ->orWhere('nombre', $nueva->nombre);
            })
            ->whereYear('created_at', $nueva->created_at->year)
            ->whereMonth('created_at', $nueva->created_at->month)
            ->orderBy('created_at')
            ->get();

        if ($candidatos->isEmpty()) {
            return;
        }

        // La maestra es la primera no duplicada; si todas lo son, la más antigua
        $maestra = $candidatos->firstWhere('estado', '!=', 'duplicada') ?? $candidatos->first();

        // Acumular datos de contacto que la maestra no tenga
        $extras = [];
        if ($nueva->telefono && $nueva->telefono !== $maestra->telefono) {
            $extras[] = 'Tel: '.$nueva->telefono;
        }
        if ($nueva->email && $nueva->email !== $maestra->email) {
            $extras[] = 'Email: '.$nueva->email;
        }
        if ($nueva->comentario && $nueva->comentario !== $maestra->comentario) {
            $extras[] = 'Comentario: '.$nueva->comentario;
        }
        if ($nueva->telefono && ! $maestra->telefono) {
            $maestra->telefono = $nueva->telefono;
        }
        if ($nueva->email && ! $maestra->email) {
            $maestra->email = $nueva->email;
        }
        if ($extras) {
            $nota = '[Duplicado #'.$nueva->id.'] '.implode('; ', $extras);
            $maestra->notas = trim(($maestra->notas ? $maestra->notas."\n" : '').$nota);
        }
        $maestra->saveQuietly();

        // Marcar la nueva como duplicada
        $nueva->estado = 'duplicada';
        $nueva->saveQuietly();

        \Illuminate\Support\Facades\Log::channel('inscripciones')->info(
            "Inscripción {$nueva->id} marcada como duplicada de {$maestra->id}"
        );
    }

    public function necesitaSeguimiento(): bool
    {
        $estadosSeguimiento = config('inscripciones.notificaciones.estados_seguimiento');

        return in_array($this->estado, $estadosSeguimiento)
            && $this->user_id !== null;
    }

    /**
     * Calcula cuándo debe enviarse la próxima notificación
     */
    public function proximaNotificacion(): ?Carbon
    {
        if (! $this->necesitaSeguimiento()) {
            return null;
        }

        // Si nunca se ha enviado notificación, es tiempo de enviar la primera
        if (! $this->ultima_notificacion) {
            return now()->subMinute(); // Devolver una fecha en el pasado para que se procese
        }

        $intervalo = $this->estado === 'asignada'
            ? config('inscripciones.notificaciones.dias_intervalo_asignada')
            : config('inscripciones.notificaciones.dias_intervalo');

        return Carbon::parse($this->ultima_notificacion)->addDays($intervalo);
    }

    /**
     * Agrega una entrada a las notas
     */
    public function comentar(string $mensaje, bool $esActividadTutor = false): void
    {
        $fecha = now()->format('d/m/Y H:i');
        $usuario = auth()->user()?->name ?? 'Sistema';

        $nuevaNota = "- {$fecha} - {$usuario}: {$mensaje}";

        $notasActuales = $this->notas ? $this->notas."\n" : '';
        $this->notas = $notasActuales.$nuevaNota;

        // Solo actualizar ultima_actividad si es una actividad real del tutor
        if ($esActividadTutor) {
            $this->ultima_actividad = now();
        }

        $this->save();
    }

    /**
     * Marca una actividad real del tutor (comentarios, cambios manuales, etc.)
     */
    public function marcarActividad(): void
    {
        $this->ultima_actividad = now();
        $this->save();
    }

    public function getEstadoEtiquetaAttribute(): string
    {
        $etiquetas = config('inscripciones.etiquetas_estados', []);

        return $etiquetas[$this->estado] ?? $this->estado;
    }
}
