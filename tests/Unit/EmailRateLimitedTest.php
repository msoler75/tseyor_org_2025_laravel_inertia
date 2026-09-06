<?php

namespace Tests\Unit;

use App\Jobs\Middleware\EmailRateLimited;
use App\Jobs\WriteFileTestJob;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class EmailRateLimitedTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Use a deterministic, in-memory cache driver for tests to avoid
        // filesystem/locks issues and to keep state isolated between tests.
        config(['cache.default' => 'array']);
        // Ensure clean state before each test
        Cache::flush();
        Cache::forget('email_rate_limit');
        Cache::forget('email_rate_limit_pending');
    }

    public function test_base_wait_returned_when_no_pending()
    {
        /**
         * Descripción: Comprueba que, sin trabajos pendientes, el tiempo
         * de espera devuelto para `BoletinEmail` es al menos el valor base
         * esperado por la middleware (600s).
         */
        $mw = new EmailRateLimited;
        $wait = $mw->getWaitTime('App\\Mail\\BoletinEmail');
        // base for BoletinEmail is 600 seconds per middleware
        $this->assertGreaterThanOrEqual(600, $wait);
    }

    public function test_wait_increases_with_pending_slots()
    {
        /**
         * Descripción: Simula que el primer slot (50) está lleno y verifica
         * que el siguiente job recibe un delay que corresponde a la siguiente
         * ventana temporal (1 * window) o al `baseWait` si es mayor.
         */
        $mw = new EmailRateLimited;

        // configure known values
        config(['mail.rate_limit.max.overall' => 50]);
        config(['mail.rate_limit.max.boletin' => 50]);
        config(['mail.rate_limit.window' => 3600]);

        // Ensure pending bucket is clean and simulate 50 pending boletin jobs
        Cache::forget('email_rate_limit_pending');
        for ($i = 0; $i < 50; $i++) {
            $mw->addPending('App\\Mail\\BoletinEmail');
        }

        // Calculate expected deterministic wait without jitter
        $expectedWindow = config('mail.rate_limit.window');
        $maxBoletin = config('mail.rate_limit.max.boletin');
        $queued = $mw->getQueuedJobsCount('App\\Mail\\BoletinEmail');
        $index = $queued + 1; // next job index
        $slot = (int) ceil($index / $maxBoletin);
        $calculated = ($slot - 1) * $expectedWindow;
        $baseWait = 600; // BoletinEmail base wait defined in middleware
        $expected = max($baseWait, $calculated);

        $wait = $mw->getWaitTime('App\\Mail\\BoletinEmail');
        $this->assertEquals($expected, $wait);
    }

    public function test_record_successful_send_removes_one_pending()
    {
        /**
         * Descripción: Añade 3 entradas pendientes y comprueba que
         * `recordSuccessfulSend()` elimina exactamente una de ellas.
         */
        $mw = new EmailRateLimited;
        // Ensure clean pending store then add 3 pending entries
        Cache::forget('email_rate_limit_pending');
        $mw->addPending('App\\Mail\\BoletinEmail');
        $mw->addPending('App\\Mail\\BoletinEmail');
        $mw->addPending('App\\Mail\\BoletinEmail');

        $before = $mw->getQueuedJobsCount('App\\Mail\\BoletinEmail');
        $this->assertEquals(3, $before);

        // Call recordSuccessfulSend which should remove one pending
        $mw->recordSuccessfulSend('App\\Mail\\BoletinEmail');

        $after = $mw->getQueuedJobsCount('App\\Mail\\BoletinEmail');
        $this->assertEquals(2, $after);
    }

    public function test_emulate_large_queue_of_1000_boletin_jobs()
    {
        /**
         * Descripción: Recorre 1000 posiciones usando únicamente la API pública
         * de `EmailRateLimited` (`getQueuedJobsCount`, `getWaitTime`, `addPending`) para
         * simular el encolado real. Para cada posición verifica que el delay
         * devuelto antes de añadir la tarea coincide exactamente con la fórmula
         * esperada: slot = ceil(index / max.overall); expected = max(baseWait, (slot-1) * window).
         */
        $mw = new EmailRateLimited;

        // configure known values
        config(['mail.rate_limit.max.overall' => 50]);
        config(['mail.rate_limit.max.boletin' => 50]);
        config(['mail.rate_limit.window' => 3600]);

        // Vamos a simular el encolado de 1000 tareas usando únicamente la API
        // pública del middleware. Para cada posición (1..1000) comprobamos el
        // `wait` que devolvería la middleware antes de añadir la tarea, y
        // luego añadimos la tarea con `addPending()` para avanzar la cola.
        $expectedWindow = config('mail.rate_limit.window');
        $maxBoletin = config('mail.rate_limit.max.boletin');
        $baseWait = 600; // BoletinEmail base wait

        // Aseguramos estado limpio
        Cache::forget('email_rate_limit_pending');

        for ($pos = 0; $pos < 1000; $pos++) {
            // queued es el número actual de pendientes antes de encolar la nueva
            $queued = $mw->getQueuedJobsCount('App\\Mail\\BoletinEmail');
            $index = $queued + 1; // índice (1-based) de la tarea que probaríamos a encolar
            $slot = (int) ceil($index / $maxBoletin);
            $calculated = ($slot - 1) * $expectedWindow;
            $expected = max($baseWait, $calculated);

            $wait = $mw->getWaitTime('App\\Mail\\BoletinEmail');
            $this->assertEquals($expected, $wait, 'Failed asserting wait for queue position='.($pos + 1));

            // Simular encolado real: añadir la tarea como pendiente
            $mw->addPending('App\\Mail\\BoletinEmail');
        }
    }

    public function test_dispatch_writefile_job_runs_with_middleware_and_respects_rate_limit()
    {
        /**
         * Descripción: Prueba el flujo end-to-end en modo `sync` para un job
         * que escribe en disco y verifica además el caso rate-limited donde
         * la middleware debe reencolar (release) y registrar la entrada
         * en la estructura `pending`.
         */
        $mw = new EmailRateLimited;

        // Ensure clean state and remove any existing log
        $path = storage_path('logs/test-email-job.txt');
        if (file_exists($path)) {
            @unlink($path);
        }

        // 1) Caso permitido: no hay timestamps en 'email_rate_limit', job debe ejecutarse
        config(['queue.default' => 'sync']);
        config(['mail.rate_limit.max.overall' => 50]);

        dispatch(new WriteFileTestJob('allowed'));

        $this->assertFileExists($path);
        $content = file_get_contents($path);
        $this->assertStringContainsString('allowed', $content);

        // Limpieza antes del siguiente caso
        @unlink($path);
        Cache::forget('email_rate_limit');
        Cache::forget('email_rate_limit_pending');

        // 2) Caso rate-limited: rellenamos el contador de envíos usando la API pública `recordSuccessfulSend()`
        $max = 10;
        config(['mail.rate_limit.max.overall' => $max]);
        // Simular que ya se han enviado $max emails en la ventana actual
        // Para poder simular release en test, creamos un proxy de job que implemente release()
        $jobProxy = new class('blocked') extends WriteFileTestJob
        {
            public $releasedDelay = null;

            public function release($delay = 0)
            {
                // Capturamos el delay para asertarlo en el test
                $this->releasedDelay = $delay;
                // no-op adicional; el middleware ya registra pending
            }
        };

        $proxyClass = get_class($jobProxy);
        for ($i = 0; $i < $max; $i++) {
            $mw->recordSuccessfulSend($proxyClass);
        }

        // Llamamos al middleware directamente (simula worker) pasando un closure que ejecutaría el job

        $mw->handle($jobProxy, function ($job) {
            // Esto sólo se ejecutaría si canSend() devuelve true
            $job->handle();
        });

        // Asegurar que la middleware intentó reencolar el job con un delay
        $this->assertNotNull($jobProxy->releasedDelay);
        $this->assertGreaterThan(0, $jobProxy->releasedDelay);

        $this->assertFileDoesNotExist($path);

        // Comprobamos mediante la API pública que hay pendientes para la clase proxy
        $this->assertGreaterThan(0, $mw->getQueuedJobsCount($proxyClass));
    }

    public function test_calculate_delay_for_index_returns_correct_delays_for_batch()
    {
        /**
         * Descripción: Prueba el método calculateDelayForIndex() que permite
         * pre-calcular delays al encolar jobs en batch, verificando que:
         * - Los primeros N jobs (dentro del límite) tienen delay 0
         * - Los siguientes N jobs tienen delay = 1 * window
         * - Los siguientes N jobs tienen delay = 2 * window
         * - Considera correctamente los jobs ya pendientes en la cola
         */
        $mw = new EmailRateLimited;

        // Configurar valores conocidos
        config(['mail.rate_limit.max.overall' => 50]);
        config(['mail.rate_limit.max.boletin' => 50]);
        config(['mail.rate_limit.window' => 3600]);

        $jobType = 'App\\Mail\\BoletinEmail';
        $maxPerWindow = $mw->getMaxSendLimit($jobType); // Obtener límite desde el middleware
        $window = $mw->getTimeWindowSeconds();

        // Caso 1: Sin jobs pendientes, calcular delays para un batch de 50
        Cache::forget('email_rate_limit_pending');

        // Primeros N jobs (slot 0) deben tener delay 0
        for ($i = 0; $i < $maxPerWindow; $i++) {
            $delay = $mw->calculateDelayForIndex($jobType, $i);
            $this->assertEquals(0, $delay, "Job {$i} should have 0 delay (slot 0)");
        }

        // Siguientes N jobs (slot 1) deben tener delay = 1 * window
        for ($i = $maxPerWindow; $i < $maxPerWindow * 2; $i++) {
            $delay = $mw->calculateDelayForIndex($jobType, $i);
            $this->assertEquals($window, $delay, "Job {$i} should have delay of 1 window (slot 1)");
        }

        // Siguientes N jobs (slot 2) deben tener delay = 2 * window
        for ($i = $maxPerWindow * 2; $i < $maxPerWindow * 3; $i++) {
            $delay = $mw->calculateDelayForIndex($jobType, $i);
            $this->assertEquals(2 * $window, $delay, "Job {$i} should have delay of 2 windows (slot 2)");
        }

        // Caso 2: Con jobs ya pendientes, verificar que se suman correctamente
        Cache::forget('email_rate_limit_pending');

        // Simular 10 jobs ya pendientes
        for ($i = 0; $i < 10; $i++) {
            $mw->addPending($jobType);
        }

        // El job en índice 0 del nuevo batch está en posición real 10 (10 pendientes + índice 0)
        // Por lo tanto debe estar en slot 0 (posición 10 < maxPerWindow)
        $delay = $mw->calculateDelayForIndex($jobType, 0);
        $this->assertEquals(0, $delay, 'With 10 pending, job at index 0 (real position 10) should be in slot 0');

        // El job en índice 5 está en posición real 15 (10 pendientes + índice 5)
        // Con maxPerWindow=50, floor(15/50) = 0 → slot 0 → delay 0
        $delay = $mw->calculateDelayForIndex($jobType, 5);
        $this->assertEquals(0, $delay, 'With 10 pending, job at index 5 (real position 15) should be in slot 0 (15 < 50)');

        // El job en índice 45 está en posición real 55 (10 pendientes + índice 45)
        // Con maxPerWindow=50, floor(55/50) = 1 → slot 1 → delay = 1 * window
        $delay = $mw->calculateDelayForIndex($jobType, 45);
        $this->assertEquals($window, $delay, 'With 10 pending, job at index 45 (real position 55) should be in slot 1');

        // El job en índice 20 está en posición real 30 (10 pendientes + índice 20)
        // Con maxPerWindow=50, floor(30/50) = 0 → slot 0 → delay 0
        $delay = $mw->calculateDelayForIndex($jobType, 20);
        $this->assertEquals(0, $delay, 'With 10 pending, job at index 20 (real position 30) should be in slot 0 (30 < 50)');
    }

    public function test_mixed_boletin_and_invitation_never_exceed_overall_limit()
    {
        /**
         * ESCENARIO CRÍTICO: 1100 boletines + 500 invitaciones simultáneas.
         * El servidor acepta ~100 emails/hora. El límite overall es 80/hora.
         * Verifica que:
         * 1. Cada tipo respeta su límite individual
         * 2. LA SUMA de ambos tipos NUNCA supera el límite overall
         * 3. Cuando overall está lleno, ambos tipos se bloquean
         */
        $mw = new EmailRateLimited;

        // Configurar límites reales (production-like)
        config(['mail.rate_limit.max.overall' => 80]);
        config(['mail.rate_limit.max.boletin' => 70]);
        config(['mail.rate_limit.max.invitacion' => 50]);
        config(['mail.rate_limit.window' => 3600]);

        $boletinType = 'App\\Mail\\BoletinEmail';
        $invitacionType = 'App\\Mail\\InvitacionEquipoEmail';

        Cache::forget('email_rate_limit_pending');

        // FASE 1: Simular envío mixto — enviar boletines e invitaciones interleaved
        // El worker procesa la cola en orden: primero default (invitaciones), luego low_priority (boletines)
        // Simulamos un escenario donde se procesan 40 boletines + 40 invitaciones = 80 total (límite overall)
        $sentBoletines = 0;
        $sentInvitaciones = 0;

        for ($i = 0; $i < 80; $i++) {
            if ($i % 2 === 0 && $sentBoletines < 70) {
                // Intentar enviar boletín
                if ($mw->isAllowedToSend($boletinType)) {
                    $mw->recordSuccessfulSend($boletinType);
                    $sentBoletines++;
                }
            } else {
                // Intentar enviar invitación
                if ($mw->isAllowedToSend($invitacionType)) {
                    $mw->recordSuccessfulSend($invitacionType);
                    $sentInvitaciones++;
                }
            }
        }

        // Deben haberse enviado exactamente 80 en total (límite overall)
        $totalSent = $sentBoletines + $sentInvitaciones;
        $this->assertEquals(80, $totalSent, 'Total sends should be exactly 80 (overall limit)');

        // FASE 2: Verificar que el siguiente envío de CUALQUIER tipo está bloqueado
        $this->assertFalse(
            $mw->isAllowedToSend($boletinType),
            'BoletinEmail should be blocked when overall limit is reached'
        );
        $this->assertFalse(
            $mw->isAllowedToSend($invitacionType),
            'InvitacionEquipoEmail should be blocked when overall limit is reached'
        );

        // FASE 3: Verificar que el tipo individual también tiene su propio límite
        // Resetear cache y llenar solo con boletines hasta su límite individual (70)
        Cache::flush();
        Cache::forget('email_rate_limit');
        Cache::forget('email_rate_limit_pending');

        for ($i = 0; $i < 70; $i++) {
            $mw->recordSuccessfulSend($boletinType);
        }

        // Boletín debe estar bloqueado (70/70), pero invitación aún puede enviar
        // (porque overall tiene slots: 80 - 70 = 10 disponibles, y invitación tiene 0 enviados)
        $this->assertFalse(
            $mw->isAllowedToSend($boletinType),
            'BoletinEmail should be blocked at its per-type limit (70)'
        );
        $this->assertTrue(
            $mw->isAllowedToSend($invitacionType),
            'InvitacionEquipoEmail should still be allowed (overall has room: 70/80)'
        );
    }

    public function test_overall_limit_blocks_even_when_per_type_limits_not_reached()
    {
        /**
         * Verifica que el límite overall actúa como techo duro:
         * si se alcanza el overall, TODOS los tipos se bloquean
         * incluso si individualmente no han llegado a su límite.
         */
        $mw = new EmailRateLimited;

        config(['mail.rate_limit.max.overall' => 30]);
        config(['mail.rate_limit.max.boletin' => 70]);
        config(['mail.rate_limit.max.invitacion' => 50]);
        config(['mail.rate_limit.window' => 3600]);

        $boletinType = 'App\\Mail\\BoletinEmail';
        $invitacionType = 'App\\Mail\\InvitacionEquipoEmail';

        Cache::forget('email_rate_limit');

        // Enviar 30 boletines (overall lleno, pero boletín individualmente en 30/70)
        for ($i = 0; $i < 30; $i++) {
            $mw->recordSuccessfulSend($boletinType);
        }

        // Overall está lleno (30/30), ambos tipos deben estar bloqueados
        $this->assertFalse($mw->isAllowedToSend($boletinType), 'Boletin blocked by overall');
        $this->assertFalse($mw->isAllowedToSend($invitacionType), 'Invitacion blocked by overall');

        // Verificar que el wait time calculado es razonable para ambos tipos
        $waitBoletin = $mw->getWaitTime($boletinType);
        $waitInvitacion = $mw->getWaitTime($invitacionType);

        $this->assertGreaterThan(0, $waitBoletin, 'Boletin wait time should be > 0 when blocked');
        $this->assertGreaterThan(0, $waitInvitacion, 'Invitacion wait time should be > 0 when blocked');
    }
}
