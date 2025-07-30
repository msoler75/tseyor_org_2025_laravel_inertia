<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Log;
class InscripcionAsignada extends Notification implements ShouldQueue
{
    use Queueable;

    public $inscripcion;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $urlGestion = route('inscripciones.mis-asignaciones');

        Log::channel('notificaciones')->info('[InscripcionAsignada] Enviando a: ' . ($notifiable->email ?? 'sin email') . ' | Nombre: ' . $notifiable->name . ' | Inscripción: ' . $this->inscripcion->nombre);

        return (new MailMessage)
            ->subject('Nueva inscripción asignada - ' . $this->inscripcion->nombre)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se te ha asignado una nueva inscripción a curso.')
            ->line('**Datos del inscrito:**')
            ->line('• Nombre: ' . $this->inscripcion->nombre)
            ->line('• Email: ' . $this->inscripcion->email)
            ->line('• Teléfono: ' . ($this->inscripcion->telefono ?? 'No especificado'))
            ->line('• Ciudad: ' . ($this->inscripcion->ciudad ?? 'No especificada'))
            ->line('• Comentario: ' . ($this->inscripcion->comentario ?? '--'))
            ->action('Gestionar Inscripciones', $urlGestion)
            ->line('Por favor, contacta con la persona inscrita lo antes posible y actualiza el estado de la inscripción.')
            ->line('Si no puedes atender esta inscripción en este momento, puedes rebotarla desde el enlace anterior.')
            ->salutation('Web Tseyor');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'inscripcion_id' => $this->inscripcion->id,
            'nombre' => $this->inscripcion->nombre,
            'email' => $this->inscripcion->email,
            'tipo' => 'inscripcion_asignada'
        ];
    }
}
