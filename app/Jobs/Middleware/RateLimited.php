<?php

namespace App\Jobs\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class RateLimited
{
    private string $rateLimitKey = 'email_rate_limit';

    public function getMaxLimit($type = "overall"): int
    {
        $max = config('mail.rate_limit.max.' . $type, 50);
        if ($max <= 0) {
            Log::warning('Invalid max limit for email rate limiter. Defaulting to 50.');
            return 50;
        }
        return $max;
    }

    public function getTimeWindow(): int
    {
        $window = config('mail.rate_limit.window', 3600);
        if ($window <= 0) {
            Log::warning('Invalid time window for email rate limiter. Defaulting to 3600 seconds.');
            return 3600;
        }
        return $window;
    }

    public function canSend(string $jobType): bool
    {
        $key = $this->rateLimitKey;

        return Cache::lock($key . ':lock', 10)->block(5, function () use ($key, $jobType) {
            $timestamps = Cache::get($key, []);

            // Filtrar los timestamps que están dentro de la ventana de tiempo
            $currentTime = time();
            $timeWindow = $this->getTimeWindow();
            $validTimestamps = array_filter($timestamps, function ($timestamp) use ($currentTime, $timeWindow) {
                return ($currentTime - $timestamp['time']) <= $timeWindow;
            });

            // Actualizar la caché con los timestamps válidos
            Cache::put($key, $validTimestamps, $timeWindow);

            // Comprobar el límite general (overall)
            if (count($validTimestamps) >= $this->getMaxLimit()) {
                return false;
            }

            // Filtrar los timestamps específicos del jobType
            $jobTimestamps = array_filter($validTimestamps, function ($timestamp) use ($jobType) {
                return $timestamp['type'] === $jobType;
            });

            // Verificar si el número de envíos está dentro del límite permitido para el jobType
            return count($jobTimestamps) < $this->getMaxLimit($jobType);
        });
    }

    public function increment(string $jobType): void
    {
        $key = $this->rateLimitKey;

        Cache::lock($key . ':lock', 10)->block(5, function () use ($key, $jobType) {
            $timestamps = Cache::get($key, []);

            // Agregar el nuevo envío con su tipo y timestamp
            $timestamps[] = ['type' => $jobType, 'time' => time()];

            // Guardar los timestamps válidos en la caché
            Cache::put($key, $timestamps, $this->getTimeWindow());
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

        if ($this->canSend($jobType)) {
            $this->increment($jobType);
            $next($job);
        } else {
            Log::channel('smtp')->info("Rate limit exceeded for job type: {$jobType}. Job will be released.");
            $job->release(config('mail.rate_limit.minutes_waiting', 5)*60); // Reencolar el trabajo después de 5 segundos
        }
    }
}
