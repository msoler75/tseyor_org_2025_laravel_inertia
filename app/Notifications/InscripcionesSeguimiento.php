<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class InscripcionesSeguimiento extends Notification implements ShouldQueue
{
    use Queueable;

    protected $pendientesDeContacto;

    public function __construct(bool $pendientesDeContacto = false)
    {
        $this->pendientesDeContacto = $pendientesDeContacto;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $urlGestion = route('inscripciones.mis-asignaciones');

        $mensaje = $this->pendientesDeContacto ?
            'Por favor, contacta con las personas inscritas lo antes posible y actualiza el estado de cada inscripción.' :
            'Tienes inscripciones asignadas que requieren tu revisión o actualización de estado.';

        $subject = $this->pendientesDeContacto ?
            'Atención: tienes inscripciones a Curso de Tseyor pendientes de contactar' :
            'Recordatorio: tienes inscripciones pendientes de revisar';

        Log::channel('notificaciones')->info('[InscripcionesSeguimiento] Enviando a: ' . ($notifiable->email ?? 'sin email') . ' | Nombre: ' . $notifiable->name . ' | Asunto: ' . $subject);
        return (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Tienes inscripciones asignadas que requieren tu revisión o actualización de estado.')
            ->action('Gestionar Inscripciones', $urlGestion)
            ->line($mensaje)
            ->line('Si no puedes atender alguna inscripción, puedes rebotarla desde el enlace anterior.')
            ->salutation('Web Tseyor');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'seguimiento_inscripciones'
        ];
    }
}
