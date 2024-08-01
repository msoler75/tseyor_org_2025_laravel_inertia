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

        return (new MailMessage)
            ->markdown('emails.incorporacion-equipo', [
                'equipo' => $this->equipo,
                'nombreUsuario' => $this->usuario->name ?? '',
                'incorporacion' => $this->incorporacion,
                'esCoordinador' => $esCoordinador,
                'equipoUrl' => $equipoUrl,
            ])
            ->subject($this->incorporacion ?
                ($esCoordinador ? 'Eres coordinador@ de ' . $this->equipo->nombre :
                    '¡Has sido incorporad@ a "' . $this->equipo->nombre . '"!')
                : 'Tu solicitud a "' . $this->equipo->nombre . '" ha sido denegada');
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
