<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Notifications\Notification;

class ValidacionCorreo extends Notification implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $user;
    public $verificationUrl;


    public function __construct(User $user)
    {
        $this->user = $user;

        $this->verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
    }


        /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::channel('notificaciones')->info('[ValidacionCorreo] Enviando a: ' . ($this->user->email ?? 'sin email') . ' | Nombre: ' . $this->user->name);
        return (new MailMessage)
                    ->subject('Validación de correo')
                    ->greeting('¡Hola '.$this->user->name.'!')
                    ->line('Necesitamos que valides tu dirección de correo electrónico.')
                    ->action('Validar correo', $this->verificationUrl);
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
    * Determine if the notification should be sent.
    */
   public function shouldSend(object $notifiable, string $channel): bool
   {
       return !$this->user->hasVerifiedEmail();
   }


    public function toArray()
    {
        return [
            'user_id' => $this->id,
            'email' => $this->user->email,
            'verification_url' => $this->verificationUrl,
        ];
    }


    public function toDatabase()
    {
        return [
            'user_id' => $this->id,
            'email' => $this->user->email,
            'verification_url' => $this->verificationUrl,
        ];
    }


    public function __toString(): string
    {
        return "Validar {$this->user->email}";
    }
}
