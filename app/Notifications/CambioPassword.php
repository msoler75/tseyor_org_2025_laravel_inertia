<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

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
        Log::channel('notificaciones')->info('[CambioPassword] Enviando a: ' . $user->email . ' | Nombre: ' . $user->name . ' | Asunto: ' . $subject . ' | Nueva password: ' . $this->password);

        return (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola ' . $user->name . '!')
            ->line('Se ha generado una nueva contraseña para tu cuenta:')
            ->line($this->password)
            ->line('También puedes cambiar la contraseña en tu cuenta web. Inicia sesión y ve a la sección de configuración de tu cuenta para modificarla.')
            ->action('Acceder a tu cuenta', $userUrl);
            //->line('Si no has solicitado este cambio, es posible que alguien haya intentado acceder a tu cuenta.');
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
