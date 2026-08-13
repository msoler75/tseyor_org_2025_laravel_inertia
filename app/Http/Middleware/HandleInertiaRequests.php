<?php

namespace App\Http\Middleware;

use App\Services\AnuncioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        // DEBUG: Log para detectar si Facebook llega aquí
        /*$userAgent = $request->header('User-Agent', '');
        if (stripos($userAgent, 'facebook') !== false) {
            \Illuminate\Support\Facades\Log::channel('validation')->info('[HandleInertiaRequests] Facebook bot detected', [
                'url' => $request->fullUrl(),
                'user_agent' => $userAgent,
                'method' => $request->method(),
                'route_uri' => $request->route() ? $request->route()->uri() : 'NO_ROUTE',
            ]);
        }*/

        $anuncioService = app(AnuncioService::class);
        $banner = $anuncioService->getBannerData();

        // llamada normal
        $r = array_merge(parent::share($request), [
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
            ],
            'anuncio' => $banner['anuncio'],
            'mantenimiento' => $banner['mantenimiento'],
            'meta_image_default' => config('seo.image.fallback'),
            'csrf_token' => csrf_token(),
            // obtener fecha y hora del servidor:
            'timestamp_server' => time(),
            'api_url' => config('app.api_url'),
            'google_analytics' => [
                'measurement_id' => config('services.google_analytics.measurement_id'),
            ],
            'equipo_interiorizacion_id' => config('equipos.interiorizacion.id'),
            'equipo_interiorizacion_slug' => config('equipos.interiorizacion.slug'),
        ]);

        // si no tiene el header X-INERTIA:
        if (! $request->header('X-Inertia')) {
            $r['initialTheme'] = Cookie::get('theme', 'light');
            $r['initialFontSize'] = Cookie::get('fontSize', 16);
        }

        // Algunas páginas se van a cachear con page-cache, así que debe estar limpia de sesión
        // si es la url de portada y no existe cabecera http de X-INERTIA:
        if ($request->route()->uri() === '/' && ! $request->header('X-Inertia')) {
            $r['auth']['user'] = null;
        }

        return $r;
    }
}
