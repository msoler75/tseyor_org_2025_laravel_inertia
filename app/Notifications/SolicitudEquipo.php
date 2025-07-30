<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\Equipo;
use App\Models\Solicitud;

class SolicitudEquipo extends Notification implements ShouldQueue
{
    use Queueable;

    private Equipo $equipo;

    /**
     * Create a new notification instance.
     */
    public function __construct(Equipo $equipo)
    {
        $this->equipo = $equipo;
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

    public function shouldSend($notifiable, $channel)
    {
        // solo se manda la notificación si hay solicitudes pendientes
        return(Solicitud::where('equipo_id', $this->equipo->id)
        ->whereNull('fecha_aceptacion')
        ->whereNull('fecha_denegacion')
        ->exists());
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/equipos/'.$this->equipo->slug . '?solicitudes');
        Log::channel('notificaciones')->info('[SolicitudEquipo(Ingreso)] Enviando a: ' . ($notifiable->email ?? 'sin email') . ' | Nombre: ' . $notifiable->name . ' | Equipo: ' . $this->equipo->nombre);
        return (new MailMessage)
                    ->subject('Solicitud de ingreso a '.$this->equipo->nombre)
                    ->line('Hay una nueva solicitud para ingresar al equipo ' . $this->equipo->nombre)
                    ->action('Ver solicitudes', $url)
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
            'equipo'=> ['nombre' => $this->equipo->nombre, 'slug' => $this->equipo->slug, 'id' => $this->equipo->id]
        ];
    }
}
