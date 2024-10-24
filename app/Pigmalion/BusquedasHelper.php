<?php

namespace App\Pigmalion;

use App\Models\Contenido;
use Illuminate\Support\Facades\Log;

class BusquedasHelper
{

    public static $palabrasComunes = [
        'a',
        'al',
        'ante',
        'bajo',
        'cabe',
        'con',
        'contra',
        'de',
        'del',
        'desde',
        'durante',
        'en',
        'entre',
        'hacia',
        'que',
        'hasta',
        'mediante',
        'para',
        'por',
        'sin',
        'sobre',
        'tras',
        'el',
        'lo',
        'los',
        'las', // 'la',
        'las',
        'un',
        'una',
        'unos',
        'unas',
        'lo', // 'alguno', 'alguna', 'algunos', 'algunas', 'ningún', 'ningun', 'ninguna',
        'ellos',
        'ellas',
        'me',
        'te',
        'se',
        'nos',
        'os',
        'le',
        'les',
        'y',
        'e',
        'o',
        'u',
        'pero',
        'sin',
        'aunque',
        'porque',
        'pues',
        'así',
        'asi',
        'entonces',
        'entonces',
        'bien',
        'además',
        'ademas',
        'tampoco',
        'sino',
        'también',
        'tambien',
        'etcétera',
        'etcétera',
        'etc.',
        'etc',
        'etc...',
    ];


    public static function descartarPalabrasComunes($busqueda)
    {
        // 1. Separar la frase $busqueda en palabras, utilizando espacios y otros símbolos de puntuación como separadores
        $palabras = preg_split('/[\s\p{P}]+/u', \App\Pigmalion\SpanishTokenizer::removeAccents(mb_strtolower($busqueda)), -1, PREG_SPLIT_NO_EMPTY);

        // 2. Descartar las palabras habituales, pronombres y artículos
        $removidas = [];
        $filtradas = [];

        foreach ($palabras as $palabra) {
            $descartar = in_array(strtolower($palabra), BusquedasHelper::$palabrasComunes);
            if ($descartar)
                $removidas[$palabra] = 1;
            else
                $filtradas[$palabra] = 1;
        }

        // 3. Devolver el string con las palabras no descartadas, y un string con palabras descartadas. Si queda vacío, se devuelve el mismo string original
        $filtradas = implode(' ', array_keys($filtradas));
        $removidas = implode(' ', array_keys($removidas));

        return [$filtradas, $removidas];
    }

    public static function separarPalabrasComunes($busqueda)
    {
        list($filtradas, $removidas) = self::descartarPalabrasComunes($busqueda);
        return empty($filtradas) ? [$busqueda, ""] : [$filtradas, $removidas];
    }


    /**
     * Valida que la búsqueda tenga palabras relevantes y no sea vacía
     */
    public static function validarBusqueda($busqueda)
    {
        if (!$busqueda)
            return false;
        list($relevante, $comunes) = self::descartarPalabrasComunes($busqueda);
        return trim($busqueda) && $relevante;
    }

    public static function formatearResultados($resultados, $busqueda, $soloTitulo = false, $extraeTodos = false)
    {
        $options = ['tagOptions' => ['class' => 'search-term']];
        $h = new ExtendedHighlighter();


        $busqueda = \App\Pigmalion\StrEx::sanitizeAndDeaccent($busqueda);

        list($words_primary, $words_secondary) = self::separarPalabrasComunes($busqueda);
        // if($words_primary===FALSE)
        // dd($words_primary, $words_secondary);

        $resultados
            ->transform(function ($item) use ($h, $options, $busqueda, $words_primary, $words_secondary, $soloTitulo, $extraeTodos) {

                // Log::info('formatearResultados ' . $item->id);

                $_x = new \App\T("BusquedasHelper", "formatearResultados_item");


                // \App\Pigmalion\AccentRemover::benchmark($item->texto);

                if ($soloTitulo)
                    unset($item['descripcion']);
                else {
                    // Limpiar el texto y eliminar elementos no deseados
                    if ($item->texto && strlen($item->texto) > 50) {

                        $textoLimpio = strip_tags($item->texto); // Eliminar etiquetas HTML
                        $textoLimpio = preg_replace('/\bimg\b/', '', $textoLimpio); // Eliminar la palabra "img" ?
                        // eliminamos caracters de markdown
                        $textoLimpio = \App\Pigmalion\Markdown::removeMarkdown($textoLimpio);
                        // $textoLimpio = preg_replace("/[#*_]/", "", $textoLimpio);
                        // $textoLimpio = preg_replace("/!?\[([^]]*)\]\(.+\)/", "$1", $textoLimpio);
                    } else
                        $textoLimpio = $item->descripcion;


                    // extraemos la parte más relevante
                    $parteRelevante = $h->extractRelevant($words_primary, $textoLimpio, 400);
                    $item->descripcion = $h->highlightPonderated($parteRelevante, $words_primary, $words_secondary, "em", $options);

                    if ($extraeTodos) {
                        // extraemos todos los extractos que contienen algo del texto buscado
                        $extractos = $h->extractRelevantAll($words_primary, $textoLimpio, 500);
                        foreach ($extractos as $idx => $extracto) {
                            $extractos[$idx] = $h->highlightPonderated($extracto, $words_primary, $words_secondary, "em", $options);
                        }
                        $item->extractos = $extractos;
                    }
                }

                // Realizar el mismo proceso para el campo 'titulo'
                if ($item->titulo)
                    $item->titulo = $h->highlightPonderated($item->titulo, $words_primary, $words_secondary, "em", $options);
                if ($item->nombre)
                    $item->nombre = $h->highlightPonderated($item->nombre, $words_primary, $words_secondary, "em", $options);

                unset($item['texto']);
                unset($item['texto_busqueda']);
                // unset($item['visibilidad']);
                return $item;
            });
    }






    public static function limpiarResultados($resultados, $busqueda, $soloTitulo = false)
    {
        $options = ['tagOptions' => ['class' => 'search-term']];
        $h = new ExtendedHighlighter();

        list($words_primary, $words_secondary) = self::separarPalabrasComunes($busqueda);

        $resultados
            ->transform(function ($item) use ($h, $options, $busqueda, $words_primary, $words_secondary, $soloTitulo) {

                if ($soloTitulo)
                    unset($item['descripcion']);
                else
                    $item->descripcion = $h->highlightPonderated($item->descripcion, $words_primary, $words_secondary, "em", $options);

                // Realizar el mismo proceso para el campo 'titulo'
                $item->titulo = $h->highlightPonderated($item->titulo, $words_primary, $words_secondary, "em", $options);

                unset($item['texto']);
                unset($item['texto_busqueda']);
                unset($item['visibilidad']);
                return $item;
            });
    }


    /**
     *
     */
    public static function buscarContenidos($buscar, $coleccion = null)
    {
        $buscar = \App\Pigmalion\StrEx::sanitizeAndDeaccent($buscar);

        list($buscarRelevante, $comunes) = BusquedasHelper::separarPalabrasComunes($buscar);

        $resultados =
            $coleccion ? Contenido::search($buscarRelevante)->where('coleccion', $coleccion)->paginate(7)
            : Contenido::search($buscarRelevante)->paginate(7); // en realidad solo se va a tomar la primera página, se supone que son los resultados más puntuados

        if (strlen($buscarRelevante) < 3)
            BusquedasHelper::limpiarResultados($resultados, $buscar, true);
        else
            BusquedasHelper::formatearResultados($resultados, $buscar, true);

        return $resultados;
    }




    /**
     * Reliza una búsqueda en el modelo, después de verificar si es una búsqueda válida
     */
    public static function buscar($model, $queryCheck, $querySearch = null)
    {
        return BusquedasHelper::validarBusqueda($queryCheck) ? $model::search($querySearch ?? $queryCheck) : $model::whereRaw("1=0");
    }
}
