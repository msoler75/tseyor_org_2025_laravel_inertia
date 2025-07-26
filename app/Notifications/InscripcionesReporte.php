<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class InscripcionesReporte extends Notification implements ShouldQueue
{
    use Queueable;

    public $estadisticas;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $estadisticas)
    {
        $this->estadisticas = $estadisticas;
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
        $mensaje = (new MailMessage)
            ->subject('Reporte diario de inscripciones - ' . now()->format('d/m/Y'))
            ->greeting('¡Hola Administrador!')
            ->line('Te enviamos el reporte diario del estado de las inscripciones:')
            ->line('');

        // Estadísticas generales
        $mensaje->line('**Estadísticas generales:**');
        foreach ($this->estadisticas['por_estado'] as $estado => $cantidad) {
            $mensaje->line("• {$estado}: {$cantidad}");
        }

        $mensaje->line('');

        // Inscripciones que requieren atención
        if (!empty($this->estadisticas['requieren_atencion'])) {
            $mensaje->line('**Inscripciones que requieren atención:**');
            foreach ($this->estadisticas['requieren_atencion'] as $inscripcion) {
                $mensaje->line("• {$inscripcion['nombre']} ({$inscripcion['estado']}) - Asignada a: {$inscripcion['usuario']}");
            }
        }

        $mensaje->line('');

        // Inscripciones rebotadas recientes
        if (!empty($this->estadisticas['rebotadas_recientes'])) {
            $mensaje->line('**Inscripciones rebotadas en las últimas 24h:**');
            foreach ($this->estadisticas['rebotadas_recientes'] as $inscripcion) {
                $mensaje->line("• {$inscripcion['nombre']} - Motivo: {$inscripcion['motivo_rebote']}");
            }
        }

        return $mensaje->action('Ver Panel de Administración', route('admin.inscripciones.dashboard'))
            ->salutation('Sistema Tseyor');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tipo' => 'reporte_inscripciones',
            'fecha' => now()->toDateString(),
            'estadisticas' => $this->estadisticas
        ];
    }
}
