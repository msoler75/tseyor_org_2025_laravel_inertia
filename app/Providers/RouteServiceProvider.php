<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('emails', function ($job) {
            return Limit::perMinute(10)->by($job->to[0]['address'] ?? 'default');
        });

        // Rate limiter general para rutas web públicas
        RateLimiter::for('web', function (Request $request) {
            return [
                // 300 peticiones por minuto por usuario/IP (5 por segundo)
                Limit::perMinute(300)->by($request->user()?->id ?: $request->ip()),
                // 3000 peticiones por hora por usuario/IP
                Limit::perHour(3000)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        // Rate limiter general para todas las rutas admin
        RateLimiter::for('admin', function (Request $request) {
            return [
                // 120 peticiones por minuto por usuario (2 por segundo)
                Limit::perMinute(120)->by($request->user()?->id ?: $request->ip()),
                // 1000 peticiones por hora por usuario
                Limit::perHour(1000)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        // Rate limiter para comandos críticos del sistema
        RateLimiter::for('command', function (Request $request) {
            return [
                // Máximo 6 comandos por minuto por usuario
                Limit::perMinute(6)->by($request->user()?->id ?: $request->ip()),
                // Máximo 30 comandos por hora por usuario
                Limit::perHour(30)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        // Rate limiter para worker management
        RateLimiter::for('worker', function (Request $request) {
            return [
                // Máximo 10 operaciones por minuto por usuario
                Limit::perMinute(10)->by($request->user()?->id ?: $request->ip()),
                // Máximo 30 operaciones por hora por usuario
                Limit::perHour(30)->by($request->user()?->id ?: $request->ip()),
            ];
        });

        $this->routes(function () {
            //Route::middleware('api')
              //  ->prefix('api')
                //->group(base_path('routes/api.php'));

            Route::middleware(['web', 'throttle:web'])
                ->group(base_path('routes/web.php'));

        });
    }
}
