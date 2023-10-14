<?php

namespace App\Pigmalion;

use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Facades\Log;
use App\Models\Pagina;

class SEO
{
    public static function get($route)
    {
        $pagina = Pagina::where('ruta', $route)->where('visibilidad', 'P')->first();

        if ($pagina)
            return [
                'seo' => new SEOData(
                    title: $pagina->titulo,
                    description: $pagina->descripcion,
                    image: $pagina->imagen ?? config('seo.image.fallback')
                )
            ];
    }


    public static function from($model)
    {
        return ['seo' => $model];
    }
}
