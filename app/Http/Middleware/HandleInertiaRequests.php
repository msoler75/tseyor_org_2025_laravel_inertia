<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tightenco\Ziggy\Ziggy;
use App\T;

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
        // si no muestra algun dato de .env, hay que borrar la cache
        /*$ajaxWords = ['buscar', 'buscar_recientes', 'buscar_archivo'];

        $isAjax = false;
        foreach ($ajaxWords as $word)
            if ($request->has($word)) {
                $isAjax = true;
                break;
            }

        // versión corta
        if ($isAjax)
            return array_merge(parent::share($request), [
                'flash' => [
                    'message' => fn() => $request->session()->get('message')
                ]
            ]);*/

/*
         $_ = new T(__CLASS__, "share:ziggy_create_Array");

        // el archivo ziggy se guarda en cache, aquí se comprueba si debe reconstruirse
        $cache_routes = base_path("bootstrap/cache/routes-v7.php");
        $cache_ziggy = base_path("bootstrap/cache/ziggy.json");
        if (
            !file_exists($cache_ziggy) ||
            !file_exists($cache_routes) ||
            filemtime($cache_routes) > filemtime($cache_ziggy)
        ) {
            $ziggy_arr = (new Ziggy)->toArray();
            file_put_contents($cache_ziggy, json_encode($ziggy_arr));
        } else {
            try {
                $ziggy_content = file_get_contents($cache_ziggy);
                $ziggy_arr = json_decode($ziggy_content, true);
            } catch (\Exception $e) {
                $ziggy_arr = (new Ziggy)->toArray(); // por si hubiera algun error
            }
        }
        $_ = null;
        // dd($ziggy_arr);


*/
        //T::xprint();
        //die;

        // dd($ziggy_arr);


        // llamada normal
        return array_merge(parent::share($request), [
            'flash' => [
                'message' => fn() => $request->session()->get('message')
            ],
            'anuncio' => config('app.anuncio'),
            'meta_image_default' => config('app.metaImageDefault'),
            'csrf_token' => csrf_token(),
            /*'ziggy' => function () use (ç$request, $ziggy_arr) {
                return array_merge($ziggy_arr, [
                    'location' => $request->url(),
                ]);
            },*/
        ]);
    }
}
