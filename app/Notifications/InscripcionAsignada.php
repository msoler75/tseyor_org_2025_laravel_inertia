<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Inscripcion;
use Illuminate\Support\Facades\Log;
class InscripcionAsignada extends Notification implements ShouldQueue
{
    use Queueable;

    public $inscripcion;

    /**
     * Create a new notification instance.
     */
    public function __construct(Inscripcion $inscripcion)
    {
        $this->inscripcion = $inscripcion;
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
        $urlGestion = route('inscripciones.mis-asignaciones');

        Log::channel('notificaciones')->info('[InscripcionAsignada] Enviando a: ' . ($notifiable->email ?? 'sin email') . ' | Nombre: ' . $notifiable->name . ' | Inscripción: ' . $this->inscripcion->nombre);

        return (new MailMessage)
            ->subject('Nueva inscripción asignada - ' . $this->inscripcion->nombre)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('Se te ha asignado una nueva inscripción a curso.')
            ->line('**Datos del inscrito:**')
            ->line('• Nombre: ' . $this->inscripcion->nombre)
            ->line('• Email: ' . $this->inscripcion->email)
            ->line('• Teléfono: ' . ($this->inscripcion->telefono ?? 'No especificado'))
            ->line('• Ciudad: ' . ($this->inscripcion->ciudad ?? 'No especificada'))
            ->line('• Comentario: ' . ($this->inscripcion->comentario ?? '--'))
            ->line('Por favor, por la ley de protección de datos recuerda que los datos del alumno son confidenciales y solo para uso de los tutores responsables.')
            ->line('Ahora puedes gestionar la inscripción en el siguiente enlace:')
            ->action('Gestionar Inscripciones', $urlGestion)
            ->line('Si no puedes atender esta inscripción en este momento, puedes rebotarla desde el enlace anterior.')
            ->line('Recomendaciones generales de tutoría:')
            ->line('• Establecer un primer contacto con la persona inscrita a la mayor brevedad posible.')
            ->line('• Orientar a la persona inscrita sobre el Curso Holístico de Tseyor y resolver cualquier duda inicial.')
            ->line('• Respetar los intereses de la persona inscrita y y considerar sus preferencias para el Curso.')
            ->line('• Apoyarse en una Casa Tseyor o Muulasterio para organizar los Cursos y llevarlos a cabo.')
            ->salutation('Web Tseyor');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'inscripcion_id' => $this->inscripcion->id,
            'nombre' => $this->inscripcion->nombre,
            'email' => $this->inscripcion->email,
            'tipo' => 'inscripcion_asignada'
        ];
    }
}
