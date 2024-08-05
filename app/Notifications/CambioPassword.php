<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Equipo;

class CambioPassword extends Notification implements ShouldQueue
{
    use Queueable;

    private string $password;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $password)
    {
        $this->password = $password;
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
        $user = $notifiable;
        $userUrl = url('/user/profile');
        $subject = 'Se ha cambiado tu acceso';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $user->name . '!')
            ->line('Se ha generado una nueva contraseña para tu cuenta:')
            ->line($this->password)
            ->line('Ahora puedes cambiar tu contraseña en tu cuenta web.')
            ->action('Acceder a tu cuenta', $userUrl)
            ->line('Si no has solicitado este cambio, por favor contacta con el soporte técnico inmediatamente.');
    }


    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user' => ['nombre' => $notifiable->name, 'id' => $notifiable->id],
            'password' => $this->password
        ];
    }
}
