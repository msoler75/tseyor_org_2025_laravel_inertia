<?php

namespace App\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class EmailRateLimited
{
    private string $rateLimitKey = 'email_rate_limit';
    private string $pendingKey = 'email_rate_limit_pending';

    /**
     * Return the maximum send limit for a given job type or overall.
     */
    public function getMaxSendLimit($type = "overall"): int
    {
        // Límites específicos por tipo de trabajo
        $limits = [
            'App\Mail\InvitacionEquipoEmail' => 100, // Más permisivo para invitaciones
            'App\Mail\BoletinEmail' => 15, // Más restrictivo para boletines
            'overall' => config('mail.rate_limit.max.overall', 50),
        ];

        $max = $limits[$type] ?? config('mail.rate_limit.max.' . $type, 50);

        if ($max <= 0) {
            Log::warning('Invalid max limit for email rate limiter. Defaulting to 50.');
            return 50;
        }
        return $max;
    }

    /**
     * Return the time window (in seconds) used for rate calculations.
     */
    public function getTimeWindowSeconds(): int
    {
        $window = config('mail.rate_limit.window', 3600);
        if ($window <= 0) {
            Log::warning('Invalid time window for email rate limiter. Defaulting to 3600 seconds.');
            return 3600;
        }
        return $window;
    }

    /**
     * Returns true if a job of the given type is allowed to be sent now.
     */
    public function isAllowedToSend(string $jobType): bool
    {
        $key = $this->rateLimitKey;
        return Cache::lock($key . ':lock', 10)->block(5, function () use ($key, $jobType) {
            $timestamps = Cache::get($key, []);

            // Filtrar los timestamps que están dentro de la ventana de tiempo
            $currentTime = time();
            $timeWindow = $this->getTimeWindowSeconds();
            $validTimestamps = array_values(array_filter($timestamps, function ($timestamp) use ($currentTime, $timeWindow) {
                return ($currentTime - $timestamp['time']) <= $timeWindow;
            }));

            // IMPORTANTE: Actualizar la caché con los timestamps válidos ANTES de contar
            Cache::put($key, $validTimestamps, $timeWindow);

            // Filtrar los timestamps específicos del jobType
            $jobTimestamps = array_filter($validTimestamps, function ($timestamp) use ($jobType) {
                return $timestamp['type'] === $jobType;
            });

            $jobCount = count($jobTimestamps);
            $jobLimit = $this->getMaxSendLimit($jobType);

            // Log para debugging
            if ($jobCount >= $jobLimit) {
                Log::channel('smtp')->debug("Rate limit check for {$jobType}: {$jobCount}/{$jobLimit} (blocked)");
            } else {
                Log::channel('smtp')->debug("Rate limit check for {$jobType}: {$jobCount}/{$jobLimit} (allowed)");
            }

            // Verificar si el número de envíos está dentro del límite permitido para el jobType
            return $jobCount < $jobLimit;
        });
    }

    /**
     * Registra un envío exitoso para el tipo de job dado.
     *
     * - Añade un timestamp en la estructura principal (`email_rate_limit`) para
     *   contabilizar envíos dentro de la ventana configurada.
     * - Utiliza un `Cache::lock` para evitar condiciones de carrera.
     * - Además, elimina una entrada de la estructura `pending` (si existe), ya
     *   que un job que pasa por `increment` significa que se procesó y no debe
     *   seguir contabilizándose como pendiente.
     *
     * Esto permite que la estimación de backlog en `getQueuedJobsCount` sea más
     * precisa cuando se usan re-lanzamientos (`$job->release($waitTime)`).
     */
    /**
     * Record a successful send for the given job type.
     * - Adds a timestamp to the main rate-limit structure.
     * - Removes one pending entry for this type if present.
     */
    public function recordSuccessfulSend(string $jobType): void
    {
        $key = $this->rateLimitKey;

        Cache::lock($key . ':lock', 10)->block(5, function () use ($key, $jobType) {
            $timestamps = Cache::get($key, []);

            // Agregar el nuevo envío con su tipo y timestamp
            $timestamps[] = ['type' => $jobType, 'time' => time()];

            // Guardar los timestamps válidos en la caché
            Cache::put($key, $timestamps, $this->getTimeWindowSeconds());

            // If there were pending entries for this type, remove one (it's been processed)
            $this->removePendingEntry($jobType);
        });
    }



    /**
     * Process the queued job.
     *
     * @param  \Closure(object): void  $next
     */
    public function handle(object $job, Closure $next): void
    {
        $jobType = get_class($job);
        if ($this->isAllowedToSend($jobType)) {
            $this->recordSuccessfulSend($jobType);
            $next($job);
        } else {
            // Tiempo de espera diferenciado por tipo de trabajo
            $waitTime = $this->getWaitTime($jobType);

            Log::channel('smtp')->info("Rate limit exceeded for job type: {$jobType}. Job will be released in {$waitTime}s.");

            // Registrar el fallo en storage/logs/envios_error.log con más contexto
            $queuedCount = $this->getQueuedJobsCount($jobType) ?? 0;
            Log::channel('envios_error')->error("Failed to send job of type: {$jobType}. Rate limit exceeded. Queued: {$queuedCount}, Waiting: {$waitTime}s");

            // Marcar como pendiente en nuestra estructura de pending
            $this->addPending($jobType);

            $job->release($waitTime); // Reencolar el trabajo después del tiempo específico
        }
    }

    public function getWaitTime(string $jobType): int
    {
        $waitTimes = [
            'App\Mail\InvitacionEquipoEmail' => 60, // 1 minuto para invitaciones (más prioritario)
            'App\Mail\BoletinEmail' => 600, // 10 minutos para boletines (menos prioritario)
        ];
        // Base/default wait for this job type
        $baseWait = $waitTimes[$jobType] ?? (config('mail.rate_limit.minutes_waiting', 5) * 60);

        // Si no podemos calcular la cola (no hay información de backlog), devolvemos el baseWait
        $maxLimit = $this->getMaxSendLimit();
        $timeWindow = $this->getTimeWindowSeconds();

        // Intentar calcular cuántos jobs del mismo tipo están pendientes en las colas
        $queuedCount = $this->getQueuedJobsCount($jobType);
        if ($queuedCount === null || $maxLimit <= 0) {
            return $baseWait;
        }

        // Estimación de índice de este job cuando se vuelva a encolar (se añadirá al final)
        $index = $queuedCount + 1;

        // Slot (1-based) de ventanas de tamaño $maxLimit
        $slot = (int) ceil($index / $maxLimit);

        // Calculamos delay como (slot - 1) * timeWindow
        $calculatedWait = max(0, ($slot - 1) * $timeWindow);

        // Asegurar que al menos devolvemos baseWait para job types más prioritarios
        $finalWait = max($baseWait, $calculatedWait);

        // Añadir pequeño jitter para evitar stampedes (0..10% de la ventana)
        // $jitter = rand(0, (int) max(0, floor($timeWindow * 0.1)));

        return $finalWait;
    }

    /**
     * Try to count pending jobs of the same type using the cache-backed `pending` list.
     * Returns null if count cannot be determined.
     */
    public function getQueuedJobsCount(string $jobType): ?int
    {
        try {
            // Use cache-backed pending queue to estimate backlog. If absent, return null.
            $pendingKey = $this->pendingKey;

            $pending = Cache::get($pendingKey, []);
            if (!is_array($pending)) {
                return null;
            }

            $count = 0;
            foreach ($pending as $item) {
                if (!is_array($item)) {
                    continue;
                }
                if (($item['type'] ?? null) === $jobType) {
                    $count++;
                }
            }

            return $count;
        } catch (\Exception $e) {
            Log::warning('Could not determine queued jobs count for rate limiter (cache): ' . $e->getMessage());
            return null;
        }
    }

    public function addPending(string $jobType): void
    {
        $pendingKey = $this->pendingKey;
        Cache::lock($pendingKey . ':lock', 10)->block(5, function () use ($pendingKey, $jobType) {
            $pending = Cache::get($pendingKey, []);
            if (!is_array($pending)) {
                $pending = [];
            }

            // Limpiar entradas viejas (más de 2 ventanas de tiempo)
            $currentTime = time();
            $maxAge = $this->getTimeWindowSeconds() * 2;
            $pending = array_values(array_filter($pending, function ($item) use ($currentTime, $maxAge) {
                return is_array($item) && ($currentTime - ($item['time'] ?? 0)) <= $maxAge;
            }));

            $pending[] = ['type' => $jobType, 'time' => time()];
            // TTL más razonable: 2 veces la ventana de tiempo
            Cache::put($pendingKey, $pending, $this->getTimeWindowSeconds() * 2);
        });
    }

    private function removePendingEntry(string $jobType): void
    {
        $pendingKey = $this->pendingKey;
        Cache::lock($pendingKey . ':lock', 10)->block(5, function () use ($pendingKey, $jobType) {
            $pending = Cache::get($pendingKey, []);
            if (!is_array($pending) || empty($pending)) {
                return;
            }

            $found = null;
            foreach ($pending as $i => $item) {
                if (is_array($item) && ($item['type'] ?? null) === $jobType) {
                    $found = $i;
                    break;
                }
            }

            if ($found !== null) {
                array_splice($pending, $found, 1);
                Cache::put($pendingKey, $pending, $this->getTimeWindowSeconds() * 10);
            }
        });
    }

    /**
     * Calculate the delay (in seconds) for queuing a job at a specific index position.
     * This allows pre-calculating delays when bulk-queuing jobs to avoid middleware re-queueing.
     *
     * @param string $jobType The fully qualified class name of the job (e.g., 'App\Mail\BoletinEmail')
     * @param int $index The position of this job in the batch (0-based)
     * @return int The delay in seconds before this job should be processed
     */
    public function calculateDelayForIndex(string $jobType, int $index): int
    {
        $maxLimit = $this->getMaxSendLimit($jobType);
        $timeWindow = $this->getTimeWindowSeconds();

        // Obtener cuántos jobs del mismo tipo ya están pendientes
        $currentQueuedCount = $this->getQueuedJobsCount($jobType) ?? 0;

        // La posición real de este job será: jobs pendientes + índice en el batch actual
        $actualPosition = $currentQueuedCount + $index;

        // Calcular en qué slot caerá (0-based)
        $slot = (int) floor($actualPosition / $maxLimit);

        // El delay es slot * timeWindow
        return $slot * $timeWindow;
    }
}
