<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Invitacion;
use App\Models\User;
use App\Models\Equipo;

class InvitacionDeclinada extends Notification implements ShouldQueue
{
    use Queueable;

    private Invitacion $invitacion;

    /**
     * Create a new notification instance.
     */
    public function __construct(Invitacion $invitacion)
    {
        $this->invitacion = $invitacion;
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
        $equipo = $this->invitacion->equipo;
        $equipoUrl = url('/equipos/' . $equipo->slug);

        $subject = 'Invitación #' . $this->invitacion->id . ' ha sido declinada';

        $invitacion = $this->invitacion;

        $usuario = $invitacion->user_id ? User::find($this->invitacion->user_id) : null;

        $dest = $usuario ? $usuario->name : $invitacion->email;

        // cast notifiable to $user
        $user = $notifiable;

        return (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $user->name . '!')

            ->line('La invitación enviada a ' . $dest . ' a formar parte del equipo "' . $equipo->nombre  . '" ha sido declinada.')
            ->action('Ver equipo', $equipoUrl);
    }
    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $equipo = $this->invitacion->equipo;
        $usuario = $this->invitacion->user_id ? User::find($this->invitacion->user_id) : null;

        return [
            'equipo' => ['nombre' => $equipo->nombre, 'slug' => $equipo->slug, 'id' => $equipo->id],
            'invitado' => $usuario ? ['usuario' => $usuario->name] : ['email' => $this->invitacion->email],
        ];
    }
}
