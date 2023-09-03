<?php

namespace App\Pigmalion;

use TeamTNT\TNTSearch\Support\Highlighter;
use Illuminate\Support\Facades\Log;

class Busquedas
{

    public static function descartarPalabrasComunes($buscar)
    {
        // 1. Separar la frase $buscar en palabras, utilizando espacios y otros símbolos de puntuación como separadores
        $palabras = preg_split('/[\s\p{P}]+/', $buscar, -1, PREG_SPLIT_NO_EMPTY);

        // 2. Descartar las palabras habituales, pronombres y artículos
        $palabrasDescartadas = [
            'a', 'al', 'ante', 'bajo', 'cabe', 'con', 'contra', 'de', 'desde', 'durante', 'en', 'entre', 'hacia', 'que',
            'hasta', 'mediante', 'para', 'por', 'sin', 'sobre', 'tras', 'el', 'la', 'los',
            'las', 'un', 'una', 'unos', 'unas', 'lo', 'alguno', 'alguna', 'algunos', 'algunas', 'ningún', 'ningun', 'ninguna',
            'ellos', 'ellas', 'me', 'te', 'se', 'nos', 'os', 'le', 'les',
            'lo', 'la', 'los', 'las', 'y', 'e', 'o', 'u', 'pero', 'sin', 'aunque', 'porque', 'pues', 'así', 'asi', 'entonces',
            'entonces', 'bien', 'además', 'ademas',
            'tampoco', 'sino', 'también', 'tambien', 'etcétera', 'etcétera', 'etc.', 'etc', 'etc...',
        ];
        $palabrasFiltradas = array_filter($palabras, function ($palabra) use ($palabrasDescartadas) {
            return !in_array(strtolower($palabra), $palabrasDescartadas);
        });

        // 3. Devolver el string con las palabras no descartadas. Si queda vacío, se devuelve el mismo string original
        $resultado = implode(' ', $palabrasFiltradas);
        return empty($resultado) ? $buscar : $resultado;
    }

    public static function formatearResultados($resultados, $busqueda)
    {
        $options  = ['tagOptions' => ['class' => 'search-term']];
        $h = new Highlighter();

        $resultados
            ->transform(function ($item) use ($h, $options, $busqueda) {

                // Log::info('formatearResultados ' . $item->id);

                // Limpiar el texto y eliminar elementos no deseados
                if ($item->texto && strlen($item->texto)>256) {

                    $textoLimpio = strip_tags($item->texto); // Eliminar etiquetas HTML
                    $textoLimpio = preg_replace('/\bimg\b/', '', $textoLimpio); // Eliminar la palabra "img"
                    // eliminamos caracters de markdown
                    $textoLimpio = preg_replace("/[#*]/", "", $textoLimpio);
                    $textoLimpio = preg_replace("/!?\[([^]]*)\]\(.+\)/", "$1", $textoLimpio);
                } else $textoLimpio = $item->descripcion;

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
