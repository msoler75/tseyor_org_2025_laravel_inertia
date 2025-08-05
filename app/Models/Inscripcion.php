<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Log;

class Inscripcion extends Model
{
    use CrudTrait;
    use Searchable;
    use \Venturecraft\Revisionable\RevisionableTrait;

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
        'notas'
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'ultima_notificacion' => 'datetime',
        'ultima_actividad' => 'datetime'
    ];

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

    /**
     * Verifica si necesita seguimiento automático
     */
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
        if (!$this->necesitaSeguimiento()) {
            return null;
        }

        $intervalo = $this->estado === 'asignada'
            ? config('inscripciones.notificaciones.dias_intervalo_asignada')
            : config('inscripciones.notificaciones.dias_intervalo');

        $base = $this->ultima_notificacion ?? $this->fecha_asignacion;
        if (!$base) return null;
        return Carbon::parse($base)->addDays($intervalo);
    }

    /**
     * Agrega una entrada a las notas
     */
    public function comentar(string $mensaje, bool $esActividadTutor = false): void
    {
        $fecha = now()->format('d/m/Y H:i');
        $usuario = auth()->user()?->name ?? 'Sistema';

        $nuevaNota = "- {$fecha} - {$usuario}: {$mensaje}";

        $notasActuales = $this->notas ? $this->notas . "\n" : '';
        $this->notas = $notasActuales . $nuevaNota;

        // Solo actualizar ultima_actividad si es una actividad real del tutor
        if ($esActividadTutor) {
            $this->ultima_actividad = now();
        }

        $this->save();
    }

    /**
     * Asigna la inscripción a un usuario
     */
    public function asignarA(User $usuario, string $motivo = 'Asignación inicial'): void
    {
        $this->user_id = $usuario->id;
        $this->estado = 'asignada';
        $this->fecha_asignacion = now();
        $this->ultima_actividad = now(); // Actividad del tutor/admin
        $this->asignado = $usuario->name; // Campo legacy para compatibilidad

        // Notificar al tutor asignado con la notificación adecuada
        try {
            $usuario->notify(new \App\Notifications\InscripcionAsignada($this));
            $this->ultima_notificacion = now(); // Solo actualizar si la notificación se envía correctamente
        } catch (\Exception $e) {
            Log::error('Error enviando notificación de asignación a tutor: ' . $e->getMessage());
            // No establecer ultima_notificacion si falla la notificación
        }

        $this->save(); // Una sola llamada a save() al final

        $this->comentar("Asignada a {$usuario->name}. {$motivo}");
    }

    /**
     * Rebota la inscripción con un motivo
     */
    public function rebotar(string $motivo): void
    {
        $usuarioAnterior = $this->usuarioAsignado;

        $this->estado = 'rebotada';
        $this->user_id = null;
        $this->setAttribute('fecha_asignacion', null);
        $this->setAttribute('ultima_notificacion', null);
        $this->ultima_actividad = now(); // Actividad del tutor

        $this->save();

        $nombreUsuario = $usuarioAnterior?->name ?? 'Usuario desconocido';
        $this->comentar("Rebotada por {$nombreUsuario}. Motivo: {$motivo}");
    }

    /**
     * Actualiza el estado con comentario
     */
    public function actualizarEstado(string $nuevoEstado, string $comentario = ''): void
    {
        $estadoAnterior = $this->estado;
        $this->estado = $nuevoEstado;
        $this->ultima_actividad = now(); // Actividad del tutor

        $this->save();

        $mensaje = "Cambiado de '{$estadoAnterior}' a '{$nuevoEstado}'";
        if ($comentario) {
            $mensaje .= ": {$comentario}";
        }

        $this->comentar($mensaje);
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
