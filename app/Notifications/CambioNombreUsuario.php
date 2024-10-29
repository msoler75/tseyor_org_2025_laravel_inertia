<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CambioNombreUsuario extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
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
        $subject = 'Se ha cambiado tu nombre de usuario';

        return (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $user->name . '!')
            ->line('Se ha cambiado tu nombre de usuario. Ahora es:')
            ->line($user->name)
            ->line('A partir de ahora utiliza siempre ese nombre (o tu correo electrónico) para acceder a tu cuenta web.')
            ->action('Ir a Mi Cuenta', $userUrl)
            ->line('Si hay un error o no estás de acuerdo con este cambio, por favor contacta con el equipo web.');
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
        ];
    }
}
