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
        'notas'
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'ultima_notificacion' => 'datetime'
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

        $intervalo = config('inscripciones.notificaciones.primer_seguimiento');

        if ($this->ultima_notificacion) {
            $intervalo = config('inscripciones.notificaciones.intervalo_seguimiento');
            return $this->ultima_notificacion->addDays($intervalo);
        }

        return $this->fecha_asignacion ? $this->fecha_asignacion->addDays($intervalo) : null;
    }

    /**
     * Agrega una entrada a las notas
     */
    public function agregarNota(string $mensaje): void
    {
        $fecha = now()->format('d/m/Y H:i');
        $usuario = auth()->user()?->name ?? 'Sistema';

        $nuevaNota = "- {$fecha} - {$usuario}: {$mensaje}";

        $notasActuales = $this->notas ? $this->notas . "\n" : '';
        $this->notas = $notasActuales . $nuevaNota;

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
        $this->ultima_notificacion = null;

        $this->save();

        $this->agregarNota("Asignada a {$usuario->name}. {$motivo}");

        // Notificar al tutor asignado con la notificación adecuada
        try {
            $usuario->notify(new \App\Notifications\InscripcionAsignada($this));
            $this->ultima_notificacion = now();
            $this->save();
        } catch (\Exception $e) {
            Log::error('Error enviando notificación de asignación a tutor: ' . $e->getMessage());
        }
    }

    /**
     * Rebota la inscripción con un motivo
     */
    public function rebotar(string $motivo): void
    {
        $usuarioAnterior = $this->usuarioAsignado;

        $this->estado = 'rebotada';
        $this->user_id = null;
        $this->fecha_asignacion = null;
        $this->ultima_notificacion = null;

        $this->save();

        $nombreUsuario = $usuarioAnterior?->name ?? 'Usuario desconocido';
        $this->agregarNota("Rebotada por {$nombreUsuario}. Motivo: {$motivo}");
    }

    /**
     * Actualiza el estado con comentario
     */
    public function actualizarEstado(string $nuevoEstado, string $comentario = ''): void
    {
        $estadoAnterior = $this->estado;
        $this->estado = $nuevoEstado;

        $this->save();

        $mensaje = "Estado cambiado de '{$estadoAnterior}' a '{$nuevoEstado}'";
        if ($comentario) {
            $mensaje .= ": {$comentario}";
        }

        $this->agregarNota($mensaje);
    }

    public function getEstadoEtiquetaAttribute(): string
    {
        $etiquetas = config('inscripciones.etiquetas_estados', []);
        return $etiquetas[$this->estado] ?? $this->estado;
    }

}
