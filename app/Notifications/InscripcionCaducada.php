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

    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Inscripción caducada')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('La inscripción de ' . $this->inscripcion->nombre . ' ha sido marcada como caducada por inactividad.')
            ->line('Estado anterior: ' . $this->inscripcion->estado)
            ->line('Fecha de última actualización: ' . $this->inscripcion->updated_at->format('d/m/Y'))
            ->line('Recuerda que los datos del alumno son confidenciales y solo para uso interno de los tutores responsables.')
            ->action('Ver inscripción', url('/inscripciones/' . $this->inscripcion->id))
            ->line('Si tienes alguna duda, contacta con el equipo web@tseyor.org');
    }
}
