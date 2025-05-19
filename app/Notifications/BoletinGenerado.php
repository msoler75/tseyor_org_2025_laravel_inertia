<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Boletin;

class BoletinGenerado extends Notification implements ShouldQueue
{
    use Queueable;

    private Boletin $boletin;

    /**
     * Create a new notification instance.
     */
    public function __construct(Boletin $boletin)
    {
        $this->boletin = $boletin;
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
    public function toMail(object $notifiable)
    {
        $urlEditarBoletin = '/admin/boletin/' . $this->boletin->id . '/edit';
        $subject = $this->boletin->titulo;
        $user = $notifiable;
        $greeting = '¡Hola ' . $user->name . '!';
        $intro = 'Se ha generado un nuevo boletín para revisión. El boletín se enviará automáticamente en 24-48 horas.';
        $actionUrl = url($urlEditarBoletin);
        $actionText = 'Editar Boletín';
        return (new MailMessage)
            ->subject("Revisa boletín: $subject")
            ->markdown('emails.boletin_generado', [
                'boletin' => $this->boletin,
                'greeting' => $greeting,
                'intro' => $intro,
                'actionUrl' => $actionUrl,
                'actionText' => $actionText,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'boletin' => ['nombre' => $this->boletin->titulo, 'id' => $this->boletin->id],
        ];
    }
}
