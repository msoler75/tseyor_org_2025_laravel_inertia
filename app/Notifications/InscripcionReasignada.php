<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Inscripcion;
use App\Models\User;

class InscripcionReasignada extends Notification
{
    use Queueable;

    public $inscripcion;
    public $nuevoTutor;

    public function __construct(Inscripcion $inscripcion, User $nuevoTutor)
    {
        $this->inscripcion = $inscripcion;
        $this->nuevoTutor = $nuevoTutor;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Inscripción reasignada')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('La inscripción de ' . $this->inscripcion->nombre . ' ha sido reasignada a otro tutor.')
            ->line('Nuevo tutor: ' . $this->nuevoTutor->name . ' (' . $this->nuevoTutor->email . ')')
            ->line('En motivo de la ley de protección de datos es necesario que borres toda información que tengas de este alumno, incluidos los correos anteriores con información de este alumno.')
            ->line('Si tienes alguna duda, contacta con Secretaría.');
    }
}
