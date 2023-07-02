<?php

namespace App\Pigmalion;

use RalphJSmit\Laravel\SEO\Support\SEOData;
use Illuminate\Support\Facades\Log;

define('SEO_DATA_FILE', '../resources/seo/SEO.json');

class SEO
{
    public static function get($route)
    {
        $data = json_decode(file_get_contents(SEO_DATA_FILE), true);
        $obj = $data[$route] ?? [
            'title' => 'Tseyor',
        ];

        // Revisa que las claves sean válidas
        $allowedKeys = ['title', 'description', 'image'];
        $objKeys = array_keys($obj);
        $diff = array_diff($objKeys, $allowedKeys);
        if (!empty($diff))
            Log::warning('Error en el archivo ' . realpath(SEO_DATA_FILE) . ' en la ruta "' . $route . '". Claves no válidas: ' . implode(', ', $diff));

        return  [
            'seo' => new SEOData(
                title: $obj['title'],
                description: $obj['description'] ?? null,
                image: $obj['image'] ?? null
            )
        ];
    }


    public static function from($model)
    {
        return ['seo' => $model];
    }
}
