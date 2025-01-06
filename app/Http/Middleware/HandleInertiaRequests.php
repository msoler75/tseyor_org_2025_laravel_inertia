<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
// use Tightenco\Ziggy\Ziggy;
// use App\T;
use App\Models\Setting;
// use Illuminate\Support\Facades\Log;

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
    public function version(Request $request): string|null
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
        $setting = Setting::where('name', 'anuncio')->first();
        $anuncio = optional($setting)->value;

        // llamada normal
        $r = array_merge(parent::share($request), [
            'flash' => [
                'message' => fn() => $request->session()->get('message')
            ],
            'anuncio' => $anuncio,
            'meta_image_default' => config('seo.image.fallback'),
            'csrf_token' => csrf_token()
        ]);

        // Algunas páginas se van a cachear con page-cache, así que debe estar limpia de sesión
        // si es la url de portada y no existe cabecera http de X-INERTIA:
        if ($request->route()->uri() === '/' && !$request->header('X-Inertia')) {
            $r['auth']['user'] = null;
        }

        return $r;
    }
}
