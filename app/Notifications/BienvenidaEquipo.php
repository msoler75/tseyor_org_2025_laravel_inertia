<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Equipo;

class BienvenidaEquipo extends Notification implements ShouldQueue
{
    use Queueable;

    private Equipo $equipo;
    private User $usuario;
    private bool $incorporacion;

    /**
     * Create a new notification instance.
     */
    public function __construct(Equipo $equipo, User $usuario, bool $incorporacion)
    {
        $this->equipo = $equipo;
        $this->usuario = $usuario;
        $this->incorporacion = $incorporacion;
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

        $esCoordinador = $this->equipo->coordinadores->contains('id', $this->usuario->id);
        $subject = $this->incorporacion ?
            ($esCoordinador ? 'Ahora coordinas el equipo "' . $this->equipo->nombre . '"' :
                '¡Eres miembro del equipo "' . $this->equipo->nombre . '"!')
            : 'Tu solicitud a "' . $this->equipo->nombre . '" ha sido denegada';

        $linea1 = $subject;

        return (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $this->usuario->name . '!')
            ->line($linea1)
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
