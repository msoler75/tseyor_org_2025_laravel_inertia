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
    protected $signature = 'inscripciones:gestionar {--solo-seguimiento : Solo enviar notificaciones de seguimiento} {--solo-reporte : Solo enviar reporte al administrador} {--id= : Procesar solo una inscripción específica por ID}';

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
        $inscripcionId = $this->option('id');

        // Detectar inscripciones caducadas antes de notificar (solo si no es ID específico)
        if (!$inscripcionId) {
            $this->detectarInscripcionesCaducadas();
        }

        if (!$soloReporte) {
            $this->enviarNotificacionesSeguimiento($inscripcionId);
        }

        if (!$soloSeguimiento && !$inscripcionId) {
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
            ->where(function($query) use ($fechaLimite) {
                $query->where('ultima_actividad', '<=', $fechaLimite)
                      ->orWhereNull('ultima_actividad'); // Para registros sin actividad registrada
            })
            ->get();

        $adminEmail = config('inscripciones.reportes.supervisor_email');
        foreach ($inscripcionesCaducables as $inscripcion) {
            // Preservar datos antes del cambio
            $estadoAnterior = $inscripcion->estado;
            $fechaUltimaActividad = $inscripcion->ultima_actividad ?? $inscripcion->updated_at;

            $inscripcion->estado = 'caducada';
            $inscripcion->save();
            $inscripcion->comentar('Inscripción marcada como caducada automáticamente por inactividad.');

            // Crear notificación con datos preservados
            $notificacion = new \App\Notifications\InscripcionCaducada($inscripcion, $estadoAnterior, $fechaUltimaActividad);
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
    private function enviarNotificacionesSeguimiento($inscripcionId = null): void
    {
        if ($inscripcionId) {
            $this->info("Buscando inscripción específica ID: {$inscripcionId}...");
        } else {
            $this->info('Buscando inscripciones que requieren seguimiento...');
        }

        $estadosFinales = config('inscripciones.notificaciones.estados_finales') ?? [];
        $estadosSeguimiento = config('inscripciones.notificaciones.estados_seguimiento');

        $this->line("Estados finales: " . implode(', ', $estadosFinales));
        $this->line("Estados seguimiento: " . implode(', ', $estadosSeguimiento));

        $query = Inscripcion::with('usuarioAsignado')
            ->whereNotNull('user_id')
            ->whereNotIn('estado', $estadosFinales)
            ->whereIn('estado', $estadosSeguimiento);

        // Si se especifica un ID, filtrar por ese ID
        if ($inscripcionId) {
            $query->where('id', $inscripcionId);
        }

        $inscripciones = $query->get();

        if ($inscripcionId && $inscripciones->isEmpty()) {
            $this->error("No se encontró la inscripción con ID: {$inscripcionId} o no cumple los criterios de seguimiento");
            return;
        }

        $this->line("Total inscripciones encontradas: " . $inscripciones->count());

        $enviadas = 0;
        $agrupadasPorUsuario = [];

        foreach ($inscripciones as $inscripcion) {
            $this->line("Evaluando inscripción #{$inscripcion->id} - Estado: {$inscripcion->estado} - Usuario: " . ($inscripcion->usuarioAsignado?->name ?? 'Sin usuario'));

            $proximaNotificacion = $inscripcion->proximaNotificacion();
            $this->line("  - Próxima notificación: " . ($proximaNotificacion ? $proximaNotificacion->format('Y-m-d H:i') : 'null'));
            $this->line("  - Es pasado: " . ($proximaNotificacion && $proximaNotificacion->isPast() ? 'SÍ' : 'NO'));
            $this->line("  - Tiene usuario asignado: " . ($inscripcion->usuarioAsignado ? 'SÍ' : 'NO'));

            if ($proximaNotificacion && $proximaNotificacion->isPast() && $inscripcion->usuarioAsignado) {
                $usuarioId = $inscripcion->usuarioAsignado->id;
                $agrupadasPorUsuario[$usuarioId]['usuario'] = $inscripcion->usuarioAsignado;
                $agrupadasPorUsuario[$usuarioId]['inscripciones'][] = $inscripcion;
                $this->line("  - AÑADIDA al grupo de usuario {$inscripcion->usuarioAsignado->name}");
            } else {
                $this->line("  - NO añadida - no cumple criterios");
            }
        }

        $this->line("Usuarios con inscripciones a notificar: " . count($agrupadasPorUsuario));

        $config = config('inscripciones.notificaciones');

        foreach ($agrupadasPorUsuario as $grupo) {
            $usuario = $grupo['usuario'];
            $this->line("Procesando usuario: {$usuario->name} con " . count($grupo['inscripciones']) . " inscripciones");

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

            $this->line("  - Inscripciones notificables después del filtro: " . count($inscripcionesNotificables));

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
            } else {
                $this->line("  - NO se envía notificación - no hay inscripciones notificables");
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
            $this->info("Enviando reporte a: {$adminEmail}");

            // Enviar por route mail (real)
            Notification::route('mail', $adminEmail)
                ->notify(new InscripcionesReporte($estadisticas));

            $this->info("✅ Reporte enviado exitosamente a: {$adminEmail}");
        } else {
            $this->warn('No se ha configurado email de administrador en config/inscripciones.php');
        }
    }

    /**
     * Genera estadísticas para el reporte
     */
    private function generarEstadisticas(): array
    {
        $this->info('📊 Iniciando generación de estadísticas...');

        $fechaLimite = now()->subMonths(3); // Cambio a 3 meses

        // Estadísticas básicas por estado (últimos 3 meses)
        $this->info('1️⃣ Generando estadísticas por estado...');
        $porEstado = Inscripcion::selectRaw('estado, COUNT(*) as total')
            ->where('created_at', '>=', $fechaLimite)
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        // DETECCIÓN DE INCIDENCIAS - PRIORIDAD ALTA
        $this->info('2️⃣ Detectando inscripciones abandonadas...');

        // 1. Inscripciones asignadas hace más de 14 días sin cambio de estado
        $inscripcionesAbandonadas = collect(Inscripcion::with('usuarioAsignado')
            ->where('estado', 'asignada')
            ->where('fecha_asignacion', '<=', now()->subDays(14))
            ->where('ultima_actividad', '<=', now()->subDays(14)) // Sin actividad reciente
            ->get()
            ->map(function ($inscripcion) {
                return [
                    'id' => $inscripcion->id,
                    'nombre' => $inscripcion->nombre,
                    'tutor' => $inscripcion->usuarioAsignado?->name ?? 'Sin asignar',
                    'tutor_id' => $inscripcion->user_id,
                    'dias_sin_actividad' => $inscripcion->ultima_actividad ?
                        $inscripcion->ultima_actividad->diffInDays(now()) :
                        $inscripcion->fecha_asignacion->diffInDays(now()),
                    'fecha_asignacion' => $inscripcion->fecha_asignacion?->format('d/m/Y'),
                    'ultima_actividad' => $inscripcion->ultima_actividad?->format('d/m/Y') ?? 'Nunca'
                ];
            }));

        $this->info('3️⃣ Analizando tutores problemáticos...');
        // 2. Tutores con múltiples incidencias (posible ausencia/pérdida de contacto)
        $tutoresProblematicos = $inscripcionesAbandonadas
            ->groupBy('tutor_id')
            ->filter(function ($inscripciones) {
                return count($inscripciones) >= 2; // 2 o más inscripciones abandonadas
            })
            ->map(function ($inscripciones, $tutorId) {
                $tutor = $inscripciones->first();
                return [
                    'tutor_id' => $tutorId,
                    'tutor_nombre' => $tutor['tutor'],
                    'total_incidencias' => $inscripciones->count(),
                    'inscripciones_afectadas' => $inscripciones->pluck('nombre')->toArray(),
                    'promedio_dias_inactividad' => round($inscripciones->avg('dias_sin_actividad')),
                    'riesgo' => $inscripciones->count() >= 3 ? 'ALTO' : 'MEDIO'
                ];
            })
            ->sortByDesc('total_incidencias')
            ->values();

        $this->info('4️⃣ Detectando notificaciones fallidas...');
        // 3. Inscripciones que nunca recibieron notificación (posible fallo técnico)
        $notificacionesFallidas = collect(Inscripcion::with('usuarioAsignado')
            ->where('estado', 'asignada')
            ->whereNull('ultima_notificacion')
            ->where('fecha_asignacion', '<=', now()->subDays(1)) // Al menos 1 día de antigüedad
            ->get()
            ->map(function ($inscripcion) {
                return [
                    'id' => $inscripcion->id,
                    'nombre' => $inscripcion->nombre,
                    'tutor' => $inscripcion->usuarioAsignado?->name ?? 'Sin asignar',
                    'dias_desde_asignacion' => $inscripcion->fecha_asignacion?->diffInDays(now()) ?? 0,
                    'fecha_asignacion' => $inscripcion->fecha_asignacion?->format('d/m/Y H:i')
                ];
            }));

        $this->info('5️⃣ Analizando tutores rebotadores...');
        // 4. Tutores con patrón de rebotes frecuentes (últimos 30 días)
        $tutoresRebotadores = Inscripcion::with('usuarioAsignado')
            ->where('estado', 'rebotada')
            ->where('updated_at', '>=', now()->subDays(30))
            ->get()
            ->groupBy('user_id')
            ->filter(function ($inscripciones) {
                return count($inscripciones) >= 2; // 2 o más rebotes en 30 días
            })
            ->map(function ($inscripciones, $tutorId) {
                $tutor = $inscripciones->first();
                return [
                    'tutor_id' => $tutorId,
                    'tutor_nombre' => $tutor->usuarioAsignado?->name ?? 'Usuario eliminado',
                    'total_rebotes' => count($inscripciones),
                    'rebotes_recientes' => $inscripciones->map(function ($ins) {
                        return [
                            'nombre' => $ins->nombre,
                            'fecha' => $ins->updated_at->format('d/m/Y')
                        ];
                    })->toArray()
                ];
            })
            ->sortByDesc('total_rebotes')
            ->values();

        $this->info('6️⃣ Detectando inscripciones estancadas...');
        // 5. Inscripciones en curso estancadas (más de 45 días sin actividad)
        $encursoEstancadas = collect(Inscripcion::with('usuarioAsignado')
            ->where('estado', 'encurso')
            ->where('ultima_actividad', '<=', now()->subDays(45))
            ->get()
            ->map(function ($inscripcion) {
                return [
                    'id' => $inscripcion->id,
                    'nombre' => $inscripcion->nombre,
                    'tutor' => $inscripcion->usuarioAsignado?->name ?? 'Sin asignar',
                    'dias_estancada' => $inscripcion->ultima_actividad?->diffInDays(now()) ?? 0,
                    'ultima_actividad' => $inscripcion->ultima_actividad?->format('d/m/Y') ?? 'Nunca'
                ];
            }));

        $this->info('7️⃣ Calculando estadísticas complementarias...');
        // ESTADÍSTICAS COMPLEMENTARIAS (últimos 3 meses)

        $inscripcionesRecientes = Inscripcion::where('created_at', '>=', now()->subDays(7))->count();
        $inscripcionesFinalizadas = Inscripcion::where('estado', 'finalizado')
            ->where('updated_at', '>=', $fechaLimite)->count();

        $tiempoPromedioResolucion = Inscripcion::where('estado', 'finalizado')
            ->where('updated_at', '>=', $fechaLimite)
            ->whereNotNull('fecha_asignacion')
            ->get()
            ->filter(function ($ins) {
                return $ins->fecha_asignacion && $ins->updated_at;
            })
            ->avg(function ($ins) {
                return $ins->fecha_asignacion->diffInDays($ins->updated_at);
            });

        $this->info('8️⃣ Analizando tutores activos...');
        // Tutores más activos (por comentarios y cambios de estado)
        $tutoresActivos = Inscripcion::with('usuarioAsignado')
            ->where('ultima_actividad', '>=', now()->subDays(30))
            ->whereNotNull('user_id')
            ->get()
            ->groupBy('user_id')
            ->map(function ($inscripciones, $tutorId) {
                $tutor = $inscripciones->first();
                return [
                    'tutor_nombre' => $tutor->usuarioAsignado?->name ?? 'Usuario eliminado',
                    'inscripciones_activas' => count($inscripciones),
                    'ultima_actividad' => $inscripciones->max('ultima_actividad')?->format('d/m/Y')
                ];
            })
            ->sortByDesc('inscripciones_activas')
            ->take(5)
            ->values();

        $this->info('9️⃣ Analizando tutores inactivos...');
        // Tutores con inscripciones abiertas pero inactivos (poca o ninguna actividad reciente)
        $estadosAbiertos = ['asignada', 'contactado', 'encurso'];
        $tutoresInactivos = Inscripcion::with('usuarioAsignado')
            ->whereIn('estado', $estadosAbiertos)
            ->whereNotNull('user_id')
            ->get()
            ->groupBy('user_id')
            ->map(function ($inscripciones, $tutorId) {
                $tutor = $inscripciones->first();
                $ultimaActividad = $inscripciones->max('ultima_actividad');
                $diasSinActividad = $ultimaActividad ?
                    $ultimaActividad->diffInDays(now()) :
                    $inscripciones->min('fecha_asignacion')?->diffInDays(now()) ?? 999;

                return [
                    'tutor_id' => $tutorId,
                    'tutor_nombre' => $tutor->usuarioAsignado?->name ?? 'Usuario eliminado',
                    'inscripciones_abiertas' => count($inscripciones),
                    'dias_sin_actividad' => $diasSinActividad,
                    'ultima_actividad' => $ultimaActividad?->format('d/m/Y') ?? 'Nunca',
                    'estados_inscripciones' => $inscripciones->pluck('estado')->unique()->toArray(),
                    'inscripciones_nombres' => $inscripciones->pluck('nombre')->toArray()
                ];
            })
            ->filter(function ($tutor) {
                // Filtrar tutores con más de 7 días sin actividad
                return $tutor['dias_sin_actividad'] >= 7;
            })
            ->sortByDesc('dias_sin_actividad')
            ->take(10) // Top 10 tutores más inactivos
            ->values();

        $this->info('🔟 Compilando resultado final...');

        return [
            // INCIDENCIAS (Prioridad alta)
            'incidencias' => [
                'inscripciones_abandonadas' => $inscripcionesAbandonadas->toArray(),
                'tutores_problematicos' => $tutoresProblematicos->toArray(),
                'notificaciones_fallidas' => $notificacionesFallidas->toArray(),
                'tutores_rebotadores' => $tutoresRebotadores->toArray(),
                'encurso_estancadas' => $encursoEstancadas->toArray(),
            ],

            // RESUMEN DE INCIDENCIAS
            'resumen_incidencias' => [
                'total_abandonadas' => $inscripcionesAbandonadas->count(),
                'total_tutores_problematicos' => $tutoresProblematicos->count(),
                'total_notificaciones_fallidas' => $notificacionesFallidas->count(),
                'total_rebotadores' => $tutoresRebotadores->count(),
                'total_estancadas' => $encursoEstancadas->count(),
            ],

            // ESTADÍSTICAS GENERALES (últimos 3 meses)
            'estadisticas_generales' => [
                'por_estado' => $porEstado,
                'inscripciones_ultima_semana' => $inscripcionesRecientes,
                'finalizadas_periodo' => $inscripcionesFinalizadas,
                'tiempo_promedio_resolucion_dias' => round($tiempoPromedioResolucion ?? 0, 1),
                'tutores_mas_activos' => $tutoresActivos->toArray(),
                'tutores_inactivos' => $tutoresInactivos->toArray(),
                'periodo_analizado' => '3 meses (desde ' . $fechaLimite->format('d/m/Y') . ')'
            ]
        ];
    }
}
