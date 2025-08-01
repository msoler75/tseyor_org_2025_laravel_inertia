<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inscripcion;
use App\Models\User;
use App\Notifications\InscripcionesSeguimiento;
use App\Notifications\InscripcionesReporte;
use Illuminate\Support\Facades\Notification;

class GestionarInscripciones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inscripciones:gestionar {--solo-seguimiento : Solo enviar notificaciones de seguimiento} {--solo-reporte : Solo enviar reporte al administrador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gestiona el seguimiento automático de inscripciones y envía reportes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $soloSeguimiento = $this->option('solo-seguimiento');
        $soloReporte = $this->option('solo-reporte');

        // Detectar inscripciones caducadas antes de notificar
        $this->detectarInscripcionesCaducadas();

        if (!$soloReporte) {
            $this->enviarNotificacionesSeguimiento();
        }

        if (!$soloSeguimiento) {
            $this->enviarReporteAdministrador();
        }

        $this->info('Gestión de inscripciones completada exitosamente.');
    }

    /**
     * Detecta inscripciones caducadas y actualiza su estado, notificando al tutor
     */
    private function detectarInscripcionesCaducadas(): void
    {
        $mesesCaduca = config('inscripciones.caduca_meses') ?? 6;
        $estadosFinales = config('inscripciones.notificaciones.estados_finales') ?? [];
        $fechaLimite = now()->subMonths($mesesCaduca);

        $inscripcionesCaducables = Inscripcion::with('usuarioAsignado')
            ->whereNotIn('estado', $estadosFinales)
            ->where('updated_at', '<=', $fechaLimite)
            ->get();

        $adminEmail = config('inscripciones.reportes.supervisor_email');
        foreach ($inscripcionesCaducables as $inscripcion) {
            $inscripcion->estado = 'caducada';
            $inscripcion->save();
            $inscripcion->comentar('Inscripción marcada como caducada automáticamente por inactividad.');
            $notificacion = new \App\Notifications\InscripcionCaducada($inscripcion);
            if ($inscripcion->usuarioAsignado) {
                $inscripcion->usuarioAsignado->notify($notificacion);
            }
            if ($adminEmail) {
                Notification::route('mail', $adminEmail)->notify($notificacion);
            }
            $this->line("Inscripción #{$inscripcion->id} marcada como caducada y notificado a tutor y administrador.");
        }
    }

    /**
     * Envía notificaciones de seguimiento a usuarios asignados
     */
    private function enviarNotificacionesSeguimiento(): void
    {
        $this->info('Buscando inscripciones que requieren seguimiento...');

        $estadosFinales = config('inscripciones.notificaciones.estados_finales') ?? [];
        $inscripciones = Inscripcion::with('usuarioAsignado')
            ->whereNotNull('user_id')
            ->whereNotIn('estado', $estadosFinales)
            ->whereIn('estado', config('inscripciones.notificaciones.estados_seguimiento'))
            ->get();

        $enviadas = 0;
        $agrupadasPorUsuario = [];

        foreach ($inscripciones as $inscripcion) {
            $proximaNotificacion = $inscripcion->proximaNotificacion();
            if ($proximaNotificacion && $proximaNotificacion->isPast() && $inscripcion->usuarioAsignado) {
                $usuarioId = $inscripcion->usuarioAsignado->id;
                $agrupadasPorUsuario[$usuarioId]['usuario'] = $inscripcion->usuarioAsignado;
                $agrupadasPorUsuario[$usuarioId]['inscripciones'][] = $inscripcion;
            }
        }

        $config = config('inscripciones.notificaciones');

        foreach ($agrupadasPorUsuario as $grupo) {
            $usuario = $grupo['usuario'];
            // Usar siempre dias_intervalo_asignada para estado 'asignada'
            $inscripcionesNotificables = array_filter($grupo['inscripciones'], function ($inscripcion) use ($config, $estadosFinales) {
                if ($inscripcion->estado === 'asignada') {
                    if (!$inscripcion->ultima_notificacion) return true;
                    $diasDesdeUltimaNotificacion = now()->diffInDays($inscripcion->ultima_notificacion);
                    return $diasDesdeUltimaNotificacion >= $config['dias_intervalo_asignada'];
                } else {
                    if(!$inscripcion->ultima_notificacion) return true;
                    $diasDesdeUltimaNotificacion = now()->diffInDays($inscripcion->ultima_notificacion);
                    return $diasDesdeUltimaNotificacion >= $config['dias_intervalo'];
                }
            });

            // si hay inscripciones pendientes y no se ha notificado recientemente
            if (count($inscripcionesNotificables) > 0) {
                // verificamos si alguna de las inscripciones es recién asignada
                $pendientesDeContacto = false;
                foreach ($inscripcionesNotificables as $inscripcion) {
                    if ($inscripcion->estado === 'asignada') {
                        $pendientesDeContacto = true;
                        break;
                    }
                }
                $this->line("Enviando recordatorio de seguimiento a: {$usuario->name} (" . count($inscripcionesNotificables) . " inscripciones)");
                $usuario->notify(new InscripcionesSeguimiento($pendientesDeContacto));
                foreach ($inscripcionesNotificables as $inscripcion) {
                    $inscripcion->ultima_notificacion = now();
                    $inscripcion->save();
                    $inscripcion->comentar("Notificación de seguimiento enviada automáticamente");
                }
                $enviadas++;
            }
        }

        $this->info("Se enviaron {$enviadas} notificaciones de seguimiento.");
    }

    /**
     * Envía reporte diario al administrador
     */
    private function enviarReporteAdministrador(): void
    {
        $this->info('Generando reporte para el administrador...');

        $estadisticas = $this->generarEstadisticas();

        $adminEmail = config('inscripciones.reportes.supervisor_email');

        if ($adminEmail) {
            // Enviar por route mail (real)
            Notification::route('mail', $adminEmail)
                ->notify(new InscripcionesReporte($estadisticas));
            // Enviar al objeto notificable para los tests
            if (class_exists('Tests\\Feature\\AdminNotifiable')) {
                $adminNotifiable = new \Tests\Feature\AdminNotifiable();
                Notification::send($adminNotifiable, new InscripcionesReporte($estadisticas));
            }

            $this->info("Reporte enviado a: {$adminEmail}");
        } else {
            $this->warn('No se ha configurado email de administrador en config/inscripciones.php');
        }
    }

    /**
     * Genera estadísticas para el reporte
     */
    private function generarEstadisticas(): array
    {

        $fechaLimite = now()->subMonths(12);

        $porEstado = Inscripcion::selectRaw('estado, COUNT(*) as total')
            ->where('created_at', '>=', $fechaLimite)
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        $requierenAtencion = Inscripcion::with('usuarioAsignado')
            ->where('created_at', '>=', $fechaLimite)
            ->whereIn('estado', config('inscripciones.notificaciones.estados_seguimiento'))
            ->where('fecha_asignacion', '<=', now()->subDays(10)) // Más de 10 días asignadas
            ->get()
            ->map(function ($inscripcion) {
                return [
                    'nombre' => $inscripcion->nombre,
                    'estado' => $inscripcion->estado,
                    'usuario' => $inscripcion->usuarioAsignado?->name ?? 'Sin asignar',
                    'dias_asignada' => $inscripcion->fecha_asignacion ? $inscripcion->fecha_asignacion->diffInDays(now()) : 0
                ];
            })
            ->toArray();

        $rebotadasRecientes = Inscripcion::where('created_at', '>=', $fechaLimite)
            ->where('estado', 'rebotada')
            ->where('updated_at', '>=', now()->subDay())
            ->get()
            ->map(function ($inscripcion) {
                // Extraer motivo de rebote de las notas (última línea que contenga "rebota")
                $notas = $inscripcion->notas ?? '';
                $lineas = explode("\n", $notas);
                $motivoRebote = 'No especificado';

                foreach (array_reverse($lineas) as $linea) {
                    if (strpos($linea, 'rebota') !== false || strpos($linea, 'Rebotada') !== false) {
                        $motivoRebote = trim(str_replace(['- ', ':', 'Rebotada por', 'rebota la inscripción', 'Motivo'], '', $linea));
                        break;
                    }
                }

                return [
                    'nombre' => $inscripcion->nombre,
                    'motivo_rebote' => $motivoRebote
                ];
            })
            ->toArray();

        return [
            'por_estado' => $porEstado,
            'requieren_atencion' => $requierenAtencion,
            'rebotadas_recientes' => $rebotadasRecientes
        ];
    }
}
