<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscripcionesAsignadas extends Notification implements ShouldQueue
{
    use Queueable;

    public $inscripciones;

    public function __construct(array $inscripciones)
    {
        $this->inscripciones = $inscripciones;
    }

    /**
     * Canales de entrega de la notificación.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Representación del email de la notificación.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $urlGestion = route('inscripciones.mis-asignaciones');
        $count = count($this->inscripciones);

        $mail = (new MailMessage)
            ->subject('Nuevas inscripciones asignadas (' . $count . ')')
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se te han asignado ' . $count . ' nuevas inscripciones a cursos.')
            ->line('Revisa y gestiona las inscripciones desde el siguiente enlace:')
            ->action('Gestionar Inscripciones', $urlGestion)
            ->line('Resumen de inscripciones asignadas:');

        foreach ($this->inscripciones as $inscripcion) {
            $mail->line('— ' . ($inscripcion['nombre'] ?? 'Sin nombre'));
        }

        $mail->line('Por favor, contacta con las personas inscritas lo antes posible y actualiza el estado de cada inscripción.')
            ->line('Si no puedes atender alguna inscripción, puedes rebotarla desde el enlace anterior.')
            ->salutation('Web Tseyor');

        return $mail;
    }

    /**
     * Representación array de la notificación.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'inscripciones' => $this->inscripciones,
            'tipo' => 'inscripciones_asignadas',
        ];
    }
}
