<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

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
        // Obtener el email del notifiable (puede ser AnonymousNotifiable o User)
        $email = $notifiable instanceof AnonymousNotifiable
            ? $notifiable->routes['mail']
            : $notifiable->email;

        Log::channel('notificaciones')->info('[InscripcionesReporte] Enviando a: '.$email);
        Log::channel('notificaciones')->info('[InscripcionesReporte] Estructura de estadísticas: '.json_encode(array_keys($this->estadisticas)));

        $baseUrl = rtrim(config('app.url'), '/');
        $resumen = $this->estadisticas['resumen_incidencias'] ?? [];
        $incidencias = $this->estadisticas['incidencias'] ?? [];
        $estadisticasGenerales = $this->estadisticas['estadisticas_generales'] ?? [];

        $hayIncidencias = ! empty($resumen) && array_sum($resumen) > 0;

        $mensaje = (new MailMessage)
            ->subject('🚨 Reporte Inscripciones - '.now()->format('d/m/Y').($hayIncidencias ? ' - INCIDENCIAS DETECTADAS' : ' - Todo OK'))
            ->greeting('¡Hola Administrador!')
            ->line($hayIncidencias ?
                '⚠️ **Se han detectado incidencias que requieren tu atención inmediata:**' :
                '✅ **Estado general satisfactorio. Aquí tienes el resumen diario:**'
            )
            ->line('');

        // 1. INCIDENCIAS CRÍTICAS PRIMERO (con enlaces directos)
        if ($hayIncidencias) {
            // Inscripciones abandonadas (más de 14 días sin actividad)
            if (($resumen['total_abandonadas'] ?? 0) > 0) {
                $mensaje->line("🚨 **{$resumen['total_abandonadas']} INSCRIPCIONES ABANDONADAS**");
                if (! empty($incidencias['inscripciones_abandonadas'])) {
                    foreach (array_slice($incidencias['inscripciones_abandonadas'], 0, 3) as $ins) {
                        $mensaje->line("• **{$ins['nombre']}** ({$ins['dias_sin_actividad']} días sin actividad)")
                            ->line("  👤 Tutor: {$ins['tutor']}");

                        // Acción individual para cada inscripción
                        $enlaceDirecto = "{$baseUrl}/admin/inscripcion/{$ins['id']}";
                        $mensaje->action("� Gestionar: {$ins['nombre']}", $enlaceDirecto);
                    }

                    // Acción para ver todas las abandonadas
                    $enlaceAbandonadas = "{$baseUrl}/admin/inscripcion?estado=asignada&dias_sin_actividad=14";
                    $mensaje->action("🚨 Ver todas las abandonadas ({$resumen['total_abandonadas']})", $enlaceAbandonadas);
                }
                $mensaje->line('');
            }

            // Tutores problemáticos
            if (($resumen['total_tutores_problematicos'] ?? 0) > 0) {
                $mensaje->line("🔴 **{$resumen['total_tutores_problematicos']} TUTORES CON MÚLTIPLES INCIDENCIAS**");
                if (! empty($incidencias['tutores_problematicos'])) {
                    foreach (array_slice($incidencias['tutores_problematicos'], 0, 3) as $tutor) {
                        $riesgo = $tutor['riesgo'] === 'ALTO' ? '🚨 ALTO RIESGO' : '⚠️ RIESGO MEDIO';
                        $mensaje->line("• **{$tutor['tutor_nombre']}**: {$tutor['total_incidencias']} incidencias ({$riesgo})");

                        // Acción para analizar cada tutor problemático
                        $enlaceTutor = "{$baseUrl}/admin/inscripcion?tutor_id={$tutor['tutor_id']}";
                        $mensaje->action("🔍 Revisar: {$tutor['tutor_nombre']}", $enlaceTutor);
                    }
                }
                $mensaje->line('');
            }

            // Notificaciones fallidas
            if (($resumen['total_notificaciones_fallidas'] ?? 0) > 0) {
                $mensaje->line("📧 **{$resumen['total_notificaciones_fallidas']} NOTIFICACIONES FALLIDAS**");
                if (! empty($incidencias['notificaciones_fallidas'])) {
                    foreach (array_slice($incidencias['notificaciones_fallidas'], 0, 3) as $ins) {
                        $mensaje->line("• **{$ins['nombre']}** ({$ins['dias_desde_asignacion']} días sin notificar)");

                        // Acción individual para cada inscripción
                        $enlaceDirecto = "{$baseUrl}/admin/inscripcion/{$ins['id']}";
                        $mensaje->action("🔧 Revisar: {$ins['nombre']}", $enlaceDirecto);
                    }

                    // Enlace para ver notificaciones fallidas
                    $enlaceFallidas = "{$baseUrl}/admin/inscripcion?sin_notificacion=1";
                    $mensaje->action("📧 Ver todas las fallidas ({$resumen['total_notificaciones_fallidas']})", $enlaceFallidas);
                }
                $mensaje->line('');
            }

            // Inscripciones estancadas en curso
            if (($resumen['total_estancadas'] ?? 0) > 0) {
                $mensaje->line("⏰ **{$resumen['total_estancadas']} INSCRIPCIONES EN CURSO ESTANCADAS**");
                if (! empty($incidencias['encurso_estancadas'])) {
                    foreach (array_slice($incidencias['encurso_estancadas'], 0, 3) as $ins) {
                        $mensaje->line("• **{$ins['nombre']}** ({$ins['dias_estancada']} días estancada)")
                            ->line("  👤 Tutor: {$ins['tutor']}");

                        // Acción individual para cada inscripción
                        $enlaceDirecto = "{$baseUrl}/admin/inscripcion/{$ins['id']}";
                        $mensaje->action("⚡ Reactivar: {$ins['nombre']}", $enlaceDirecto);
                    }

                    // Enlace para ver todas las estancadas
                    $enlaceEstancadas = "{$baseUrl}/admin/inscripcion?estado=encurso&dias_sin_actividad=45";
                    $mensaje->action("⏰ Ver todas las estancadas ({$resumen['total_estancadas']})", $enlaceEstancadas);
                }
                $mensaje->line('');
            }

            // Tutores rebotadores
            if (($resumen['total_rebotadores'] ?? 0) > 0) {
                $mensaje->line("🔄 **{$resumen['total_rebotadores']} TUTORES CON REBOTES FRECUENTES**");
                if (! empty($incidencias['tutores_rebotadores'])) {
                    foreach (array_slice($incidencias['tutores_rebotadores'], 0, 3) as $tutor) {
                        $mensaje->line("• {$tutor['tutor_nombre']}: {$tutor['total_rebotes']} rebotes en 30 días");
                    }
                    // Enlace para ver rebotes
                    $enlaceRebotes = "{$baseUrl}/admin/inscripcion?estado=rebotada";
                    $mensaje->action("� Analizar rebotes frecuentes ({$resumen['total_rebotadores']} tutores)", $enlaceRebotes);
                }
                $mensaje->line('');
            }
        }

        // 2. RESUMEN ESTADÍSTICO (más compacto)
        $mensaje->line('📊 **RESUMEN GENERAL**');

        // Datos de actividad reciente
        $nuevasEstaSemana = $estadisticasGenerales['inscripciones_ultima_semana'] ?? 0;
        $finalizadas = $estadisticasGenerales['finalizadas_periodo'] ?? 0;
        $tiempoPromedio = $estadisticasGenerales['tiempo_promedio_resolucion_dias'] ?? 0;

        $mensaje->line("• 🆕 Nuevas esta semana: **{$nuevasEstaSemana}**")
            ->line("• ✅ Finalizadas (3 meses): **{$finalizadas}**")
            ->line("• ⏱️ Tiempo promedio resolución: **{$tiempoPromedio} días**");

        // Estados actuales (solo los más relevantes)
        if (isset($estadisticasGenerales['por_estado']) && ! empty($estadisticasGenerales['por_estado'])) {
            $estados = $estadisticasGenerales['por_estado'];
            $total = array_sum($estados);
            $mensaje->line("• 📋 Total inscripciones: **{$total}**");

            // Mostrar solo estados con más de 0 inscripciones
            foreach (['asignada', 'contactado', 'encurso', 'finalizado'] as $estado) {
                if (($estados[$estado] ?? 0) > 0) {
                    $emoji = match ($estado) {
                        'asignada' => '📝',
                        'contactado' => '📞',
                        'encurso' => '🔄',
                        'finalizado' => '✅',
                        default => '📄'
                    };
                    $mensaje->line("  {$emoji} {$estado}: {$estados[$estado]}");
                }
            }
        }

        $mensaje->line('');

        // 3. TUTORES DESTACADOS (solo si no hay incidencias críticas)
        if (! $hayIncidencias && ! empty($estadisticasGenerales['tutores_mas_activos'])) {
            $mensaje->line('⭐ **Top 3 Tutores Activos:**');
            foreach (array_slice($estadisticasGenerales['tutores_mas_activos'], 0, 3) as $tutor) {
                $mensaje->line("• {$tutor['tutor_nombre']}: {$tutor['inscripciones_activas']} activas");
            }
            $mensaje->line('');
        }

        // 4. ENLACES DE ACCIÓN RÁPIDA
        $mensaje->line('🔗 **ACCESOS RÁPIDOS:**');

        $enlaceGeneral = "{$baseUrl}/admin/inscripcion";
        $enlacePendientes = "{$baseUrl}/admin/inscripcion?estado=asignada";
        $enlaceEnCurso = "{$baseUrl}/admin/inscripcion?estado=encurso";

        return $mensaje->action('📊 Panel General de Inscripciones', $enlaceGeneral)
            ->action('📝 Ver Inscripciones Pendientes', $enlacePendientes)
            ->action('🔄 Ver Inscripciones en Curso', $enlaceEnCurso)
            ->salutation('Equipo Web Tseyor');
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
            'estadisticas' => $this->estadisticas,
        ];
    }
}
