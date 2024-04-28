<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\User;
use App\Models\Equipo;

class AbandonoEquipo extends Notification implements ShouldQueue
{
    use Queueable;

    private Equipo $equipo;
    private User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Equipo $equipo, User $user)
    {
        $this->equipo = $equipo;
        $this->user = $user;
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
        $url = url('/equipos/'.$this->equipo->slug);
        $texto = $this->user->name . ' ha abandonado el equipo '.$this->equipo->nombre;

        return (new MailMessage)
                    // ->line($this->user->name. ' ha abandonado el equipo ' . $this->equipo->nombre)
                    // ->action('Ver Equipo', $url)
                    // ->line('Thank you for using our application!')
                    ->subject($texto)
                    ->markdown('emails.notificacion', ['texto'=>'Te informamos que '.$texto, 'action'=>'Ver Equipo', 'url'=>$url]);
                    ;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'equipo'=> ['nombre' => $this->equipo->nombre, 'slug' => $this->equipo->slug, 'id' => $this->equipo->id],
            'user' => ['nombre' => $this->user->name, 'id'=>$this->user->id]
        ];
    }
}
