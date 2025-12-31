<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use App\Jobs\Middleware\EmailRateLimited;
use App\Jobs\WriteFileTestJob;

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
        $mw = new EmailRateLimited();
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
        $mw = new EmailRateLimited();

        // configure known values
        config(['mail.rate_limit.max.overall' => 50]);
        config(['mail.rate_limit.window' => 3600]);

        // Ensure pending bucket is clean and simulate 50 pending boletin jobs
        Cache::forget('email_rate_limit_pending');
        for ($i = 0; $i < 50; $i++) {
            $mw->addPending('App\\Mail\\BoletinEmail');
        }

        // Calculate expected deterministic wait without jitter
        $expectedWindow = config('mail.rate_limit.window');
        $maxOverall = config('mail.rate_limit.max.overall');
        $queued = $mw->getQueuedJobsCount('App\\Mail\\BoletinEmail');
        $index = $queued + 1; // next job index
        $slot = (int) ceil($index / $maxOverall);
        $calculated = ($slot - 1) * $expectedWindow;
        $baseWait = 600; // BoletinEmail base wait defined in middleware
        $expected = max($baseWait, $calculated);

        $wait = $mw->getWaitTime('App\\Mail\\BoletinEmail');
        $this->assertEquals($expected, $wait);
    }

    public function test_recordSuccessfulSend_removes_one_pending()
    {
        /**
         * Descripción: Añade 3 entradas pendientes y comprueba que
         * `recordSuccessfulSend()` elimina exactamente una de ellas.
         */
        $mw = new EmailRateLimited();
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
        $mw = new EmailRateLimited();

        // configure known values
        config(['mail.rate_limit.max.overall' => 50]);
        config(['mail.rate_limit.window' => 3600]);

        // Vamos a simular el encolado de 1000 tareas usando únicamente la API
        // pública del middleware. Para cada posición (1..1000) comprobamos el
        // `wait` que devolvería la middleware antes de añadir la tarea, y
        // luego añadimos la tarea con `addPending()` para avanzar la cola.
        $expectedWindow = config('mail.rate_limit.window');
        $maxOverall = config('mail.rate_limit.max.overall');
        $baseWait = 600; // BoletinEmail base wait

        // Aseguramos estado limpio
        Cache::forget('email_rate_limit_pending');

        for ($pos = 0; $pos < 1000; $pos++) {
            // queued es el número actual de pendientes antes de encolar la nueva
            $queued = $mw->getQueuedJobsCount('App\\Mail\\BoletinEmail');
            $index = $queued + 1; // índice (1-based) de la tarea que probaríamos a encolar
            $slot = (int) ceil($index / $maxOverall);
            $calculated = ($slot - 1) * $expectedWindow;
            $expected = max($baseWait, $calculated);

            $wait = $mw->getWaitTime('App\\Mail\\BoletinEmail');
            $this->assertEquals($expected, $wait, "Failed asserting wait for queue position=" . ($pos + 1));

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
        $mw = new EmailRateLimited();

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
        $jobProxy = new class('blocked') extends WriteFileTestJob {
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
}
