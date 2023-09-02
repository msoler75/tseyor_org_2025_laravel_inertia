<?php

namespace App\Pigmalion;

use TeamTNT\TNTSearch\Support\Highlighter;
use Illuminate\Support\Facades\Log;

class Busquedas
{

    public static function formatearResultados($resultados, $busqueda)
    {
        $options  = ['tagOptions' => ['class' => 'search-term']];
        $h = new Highlighter();

        $resultados
            ->transform(function ($item) use ($h, $options, $busqueda) {

                Log::info('formatearResultados '. $item->id);

                // Limpiar el texto y eliminar elementos no deseados
                $textoLimpio = strip_tags($item->texto); // Eliminar etiquetas HTML
                $textoLimpio = preg_replace('/\bimg\b/', '', $textoLimpio); // Eliminar la palabra "img"
                // eliminamos caracters de markdown
                $textoLimpio = preg_replace("/[#*]/", "", $textoLimpio);
                $textoLimpio = preg_replace("/!?\[([^]]*)\]\(.+\)/", "$1", $textoLimpio);

                // extraemos la parte más relevante
                $parteRelevante = $h->extractRelevant($busqueda, $textoLimpio, 400);

                $item->descripcion = $h->highlight($parteRelevante, $busqueda, "em", $options);

                // Realizar el mismo proceso para el campo 'titulo'
                $item->titulo = $h->highlight($item->titulo, $busqueda, "em", $options);

                unset($item['texto']);
                return $item;
            });
    }


    public static function limpiarResultados($resultados, $busqueda)
    {
        $options  = ['tagOptions' => ['class' => 'search-term']];
        $h = new Highlighter();

        $resultados
            ->transform(function ($item) use ($h, $options, $busqueda) {

                $item->descripcion = $h->highlight($item->descripcion, $busqueda, "em", $options);

                // Realizar el mismo proceso para el campo 'titulo'
                $item->titulo = $h->highlight($item->titulo, $busqueda, "em", $options);

                unset($item['texto']);
                return $item;
            });
    }
}
