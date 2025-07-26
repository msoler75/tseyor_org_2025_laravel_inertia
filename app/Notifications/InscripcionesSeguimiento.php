<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscripcionesSeguimiento extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urlGestion = route('inscripciones.mis-asignaciones');

        return (new MailMessage)
            ->subject('Recordatorio: tienes inscripciones pendientes de revisar')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tienes inscripciones asignadas que requieren tu revisión o actualización de estado.')
            ->action('Gestionar Inscripciones', $urlGestion)
            ->line('Por favor, revisa y actualiza el estado de las inscripciones para mantener el seguimiento correcto.')
            ->salutation('Web Tseyor');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'seguimiento_inscripciones'
        ];
    }
}
