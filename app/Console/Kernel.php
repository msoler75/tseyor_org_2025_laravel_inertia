<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        Log::channel('jobs')->info('Iniciando ejecución del schedule de tareas programadas');

        // Variables de entorno necesarias para los scripts bash
        $deployUser = config('app.deploy_user');
        $boletinToken = config('app.boletin.token');

        // ===== QUEUE WORKER (reemplaza el cron @hourly worker-start.sh) =====
        // Ejecuta workers temporales cada minuto que terminan automáticamente
        $schedule->command('queue:work --queue=default,low_priority,audio_processing --sleep=3 --tries=10 --stop-when-empty --timeout=120 --max-jobs=300')
                ->everyFiveMinutes()  // Ejecuta cada 5 minutos
                 ->withoutOverlapping()
                 ->appendOutputTo(storage_path('logs/queue-worker.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Queue Worker'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Queue Worker'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Queue Worker'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Queue Worker'); });

        // ===== SSR (Server Side Rendering) =====
        // tseyor.org SSR start (minuto 01 cada hora)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/ssr.sh') . ' start')
             ->hourlyAt(21)
             ->appendOutputTo(storage_path('logs/ssr-start.log'))
             ->before(function () { Log::channel('jobs')->info('Iniciando tarea: SSR Start'); })
             ->after(function () { Log::channel('jobs')->info('Completada tarea: SSR Start'); })
             ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: SSR Start'); })
             ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: SSR Start'); });

        // tseyor.org SSR restart (cada 12 horas)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/ssr.sh') . ' restart')
             ->twiceDailyAt(1, 13, 15) // Run the task daily at 1:15 & 13:15
             ->appendOutputTo(storage_path('logs/ssr-start.log'))
             ->before(function () { Log::channel('jobs')->info('Iniciando tarea: SSR Restart'); })
             ->after(function () { Log::channel('jobs')->info('Completada tarea: SSR Restart'); })
             ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: SSR Restart'); })
             ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: SSR Restart'); });

        Log::channel('jobs')->info('Finalizada ejecución del schedule de tareas programadas');

        return;

        // ===== COPIAS DE SEGURIDAD =====
        // tseyor.org database backup daily (03:05 diario)
        $schedule->command('db:backup')
                 ->dailyAt('03:05')
                 ->appendOutputTo(storage_path('logs/db-backup.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Database Backup'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Database Backup'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Database Backup'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Database Backup'); });

        // ===== SEO Y OPTIMIZACIÓN =====
        // tseyor.org Sitemap.xml diario (02:05 diario)
        $schedule->command('sitemap:generate')
                 ->dailyAt('02:05')
                 ->appendOutputTo(storage_path('logs/sitemap.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Sitemap Generate'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Sitemap Generate'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Sitemap Generate'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Sitemap Generate'); });

        // ===== COMUNICACIONES Y CORREOS =====
        // check-mail notificaciones daily (06:10 diario)
        $schedule->command('check-bounced notificaciones --hours=26')
                 ->dailyAt('06:10')
                 ->appendOutputTo(storage_path('logs/check-bounced.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Check Bounced Emails'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Check Bounced Emails'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Check Bounced Emails'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Check Bounced Emails'); });

        // ===== BOLETINES =====
        // tseyor.org boletin mensual (04:40 día 1 de cada mes)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} mensual")
                 ->monthlyOn(1, '04:40')
                 ->appendOutputTo(storage_path('logs/boletin-mensual.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Boletín Mensual'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Boletín Mensual'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Boletín Mensual'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Boletín Mensual'); });

        // tseyor.org boletin quincenal (06:15 días 1 y 15)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} quincenal")
                 ->monthlyOn(1, '06:15')
                 ->appendOutputTo(storage_path('logs/boletin-quincenal.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Boletín Quincenal Día 1'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Boletín Quincenal Día 1'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Boletín Quincenal Día 1'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Boletín Quincenal Día 1'); });

        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} quincenal")
                 ->monthlyOn(15, '06:15')
                 ->appendOutputTo(storage_path('logs/boletin-quincenal.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Boletín Quincenal Día 15'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Boletín Quincenal Día 15'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Boletín Quincenal Día 15'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Boletín Quincenal Día 15'); });

        // tseyor.org boletin semanal (02:30 sábados)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} semanal")
                 ->saturdays()
                 ->at('02:30')
                 ->appendOutputTo(storage_path('logs/boletin-semanal.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Boletín Semanal'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Boletín Semanal'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Boletín Semanal'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Boletín Semanal'); });

        // tseyor.org boletines enviar diario (@daily)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_enviar_pendientes.sh') . " {$boletinToken}")
                 ->dailyAt('08:30')
                 ->appendOutputTo(storage_path('logs/boletin-enviar.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Enviar Boletines Pendientes'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Enviar Boletines Pendientes'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Enviar Boletines Pendientes'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Enviar Boletines Pendientes'); });

        // ===== GESTIÓN DE INSCRIPCIONES =====
        // Gestionar inscripciones diario (05:30 diario)
        $schedule->command('inscripciones:gestionar')
                 ->dailyAt('05:30')
                 ->appendOutputTo(storage_path('logs/inscripciones.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Gestionar Inscripciones'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Gestionar Inscripciones'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Gestionar Inscripciones'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Gestionar Inscripciones'); });

        // ===== MONITOREO DEL SISTEMA =====
        // check_system_load (cada 2 horas a los 30 minutos)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/check_system_load.sh'))
                 ->cron('30 */2 * * *')
                 ->appendOutputTo(storage_path('logs/system_load.log'))
                 ->before(function () { Log::channel('jobs')->info('Iniciando tarea: Check System Load'); })
                 ->after(function () { Log::channel('jobs')->info('Completada tarea: Check System Load'); })
                 ->onFailure(function () { Log::channel('jobs')->error('Falló tarea: Check System Load'); })
                 ->onSuccess(function () { Log::channel('jobs')->info('Éxito en tarea: Check System Load'); });

        Log::channel('jobs')->info('Finalizada ejecución del schedule de tareas programadas');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
