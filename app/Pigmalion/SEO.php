<?php

namespace App\Pigmalion;

use App\Models\Pagina;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class SEO
{
    public static function get($route)
    {
        $pagina = Pagina::where('ruta', $route)->publicada()->first();

        if ($pagina) {
            return [
                'seo' => new SEOData(
                    title: $pagina->titulo,
                    description: $pagina->descripcion,
                    image: $pagina->imagen ?? config('seo.image.fallback')
                ),
            ];
        }
    }

    public static function from($model)
    {
        return ['seo' => $model];
    }
}
