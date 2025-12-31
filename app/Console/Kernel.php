<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Variables de entorno necesarias para los scripts bash
        $deployUser = config('app.deploy_user');
        $boletinToken = config('app.boletin.token');

        // ===== QUEUE WORKER (reemplaza el cron @hourly worker-start.sh) =====
        // Ejecuta workers temporales cada minuto que terminan automáticamente
        $schedule->command('queue:work --queue=default,low_priority,audio_processing --sleep=3 --tries=10 --stop-when-empty --timeout=120 --max-jobs=100')
                 ->everyMinute()
                 ->withoutOverlapping(60)
                 ->appendOutputTo(storage_path('logs/queue-worker.log'));

        // ===== SSR (Server Side Rendering) =====
        // tseyor.org SSR start (minuto 01 cada hora)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/ssr.sh') . ' start')
             ->hourlyAt(21)
             ->appendOutputTo(storage_path('logs/ssr-start.log'));

                 return;

        // ===== COPIAS DE SEGURIDAD =====
        // tseyor.org database backup daily (03:05 diario)
        $schedule->command('db:backup')
                 ->dailyAt('03:05')
                 ->appendOutputTo(storage_path('logs/db-backup.log'));

        // ===== SEO Y OPTIMIZACIÓN =====
        // tseyor.org Sitemap.xml diario (02:05 diario)
        $schedule->command('sitemap:generate')
                 ->dailyAt('02:05')
                 ->appendOutputTo(storage_path('logs/sitemap.log'));

        // ===== COMUNICACIONES Y CORREOS =====
        // check-mail notificaciones daily (06:10 diario)
        $schedule->command('check-bounced notificaciones --hours=26')
                 ->dailyAt('06:10')
                 ->appendOutputTo(storage_path('logs/check-bounced.log'));

        // ===== BOLETINES =====
        // tseyor.org boletin mensual (04:40 día 1 de cada mes)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} mensual")
                 ->monthlyOn(1, '04:40')
                 ->appendOutputTo(storage_path('logs/boletin-mensual.log'));

        // tseyor.org boletin quincenal (06:15 días 1 y 15)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} quincenal")
                 ->monthlyOn(1, '06:15')
                 ->appendOutputTo(storage_path('logs/boletin-quincenal.log'));

        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} quincenal")
                 ->monthlyOn(15, '06:15')
                 ->appendOutputTo(storage_path('logs/boletin-quincenal.log'));

        // tseyor.org boletin semanal (02:30 sábados)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_preparar.sh') . " {$boletinToken} semanal")
                 ->saturdays()
                 ->at('02:30')
                 ->appendOutputTo(storage_path('logs/boletin-semanal.log'));

        // tseyor.org boletines enviar diario (@daily)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/boletin_enviar_pendientes.sh') . " {$boletinToken}")
                 ->dailyAt('08:30')
                 ->appendOutputTo(storage_path('logs/boletin-enviar.log'));

        // ===== GESTIÓN DE INSCRIPCIONES =====
        // Gestionar inscripciones diario (05:30 diario)
        $schedule->command('inscripciones:gestionar')
                 ->dailyAt('05:30')
                 ->appendOutputTo(storage_path('logs/inscripciones.log'));

        // ===== MONITOREO DEL SISTEMA =====
        // check_system_load (cada 2 horas a los 30 minutos)
        $schedule->exec("DEPLOY_USER={$deployUser} " . base_path('bash/check_system_load.sh'))
                 ->cron('30 */2 * * *')
                 ->appendOutputTo(storage_path('logs/system_load.log'));
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
