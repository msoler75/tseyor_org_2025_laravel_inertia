<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Equipo;

class DenegadoEquipo extends Notification implements ShouldQueue
{
    use Queueable;

    private Equipo $equipo;
    private User $usuario;

    /**
     * Create a new notification instance.
     */
    public function __construct(Equipo $equipo, User $usuario)
    {
        $this->equipo = $equipo;
        $this->usuario = $usuario;
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
        $equipoUrl = url('/equipos/' . $this->equipo->slug);

        $subject = 'Tu solicitud a "' . $this->equipo->nombre . '" ha sido denegada';

        $linea1 = $subject;

        return (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $this->usuario->name . '!')
            ->line($linea1 . '. Puedes volver a solicitarlo más adelante.')
            ->action('Ver equipo', $equipoUrl);
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'equipo' => ['nombre' => $this->equipo->nombre, 'slug' => $this->equipo->slug, 'id' => $this->equipo->id],
            'user' => ['nombre' => $this->usuario->name, 'id' => $this->usuario->id]
        ];
    }
}
