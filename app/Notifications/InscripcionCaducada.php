<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Inscripcion;

class InscripcionCaducada extends Notification
{
    use Queueable;

    public $inscripcion;
    public $estadoAnterior;
    public $fechaUltimaActualizacion;

    public function __construct(Inscripcion $inscripcion, $estadoAnterior = null, $fechaUltimaActualizacion = null)
    {
        $this->inscripcion = $inscripcion;
        $this->estadoAnterior = $estadoAnterior;
        $this->fechaUltimaActualizacion = $fechaUltimaActualizacion;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $estadoMostrar = $this->estadoAnterior ?? $this->inscripcion->estado;
        $fechaMostrar = $this->fechaUltimaActualizacion ?? $this->inscripcion->updated_at;
        $enlaceGestion = rtrim(config('app.url'),'/') . '/admin/inscripcion/' . $this->inscripcion->id . '/edit';

        return (new MailMessage)
            ->subject('Inscripción caducada')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('La inscripción de ' . $this->inscripcion->nombre . ' ha sido marcada como caducada por inactividad.')
            ->line('Estado anterior: ' . $estadoMostrar)
            ->line('Fecha de última actualización: ' . $fechaMostrar->format('d/m/Y'))
            ->line('Recuerda que los datos del alumno son confidenciales y solo para uso interno de los tutores responsables.')
            ->action('Ver inscripción', $enlaceGestion)
            ->line('Si tienes alguna duda, contacta con el equipo web@tseyor.org');
    }
}
