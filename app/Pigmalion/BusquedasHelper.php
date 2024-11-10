<?php

namespace App\Pigmalion;

use App\Models\Contenido;
use Illuminate\Support\Facades\Log;

define('SEARCH_RESULTS_FRAGMENT_SIZE', 300);

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
        'este',
        'esta',
        'estan',
        'es',
        'son',
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
        $k = "xzzzzzzhhhhhhhx";
        $busqueda = str_replace("-", $k, $busqueda);
        // 1. Separar la frase $busqueda en palabras, utilizando espacios y otros símbolos de puntuación como separadores
        $palabras = preg_split('/[\s\p{P}]+/u', StrEx::removerAcentos(mb_strtolower($busqueda)), -1, PREG_SPLIT_NO_EMPTY);

        $palabras = array_map(function ($x) use ($k) {
            return str_replace($k, "-", $x);
        }, $palabras);

        // 2. Descartar las palabras habituales, pronombres y artículos
        $removidas = [];
        $filtradas = [];

        foreach ($palabras as $palabra) {
            $palabraLimpia = str_replace("-", "", $palabra);
            $descartar = in_array($palabraLimpia, BusquedasHelper::$palabrasComunes);
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

        $busqueda_original = $busqueda;

        $busqueda_original_limpia = StrEx::removerAcentos(mb_strtolower($busqueda));

        $busqueda = trim(StrEx::sanitizeAndDeaccent(mb_strtolower($busqueda)));

        list($frase_exacta, $busqueda_sin_frase) = self::obtenerFraseExacta($busqueda);
        if ($frase_exacta)
            $busqueda = $busqueda_sin_frase;

        list($words_primary, $words_secondary) = self::separarPalabrasComunes($busqueda);
        // if($words_primary===FALSE)
        // dd($words_primary, $words_secondary);

        $resultados
            ->transform(function ($item) use ($h, $options, $busqueda, $busqueda_original_limpia, $frase_exacta, $busqueda_sin_frase, $words_primary, $words_secondary, $soloTitulo, $extraeTodos) {

                // Log::info('formatearResultados ' . $item->id);
                $_x = new \App\T("BusquedasHelper", "formatearResultados_item");

                $busqueda_primaria = $words_primary;
                $busqueda_secundaria = $words_secondary;

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

                    // $textoLimpio = StrEx::removerAcentos(mb_strtolower($textoLimpio));

                    if ($frase_exacta) {
                        $words      = preg_split($h->getTokenizer()->getPattern(), $words_primary, -1, PREG_SPLIT_NO_EMPTY);
                        $busqueda_primaria = [$frase_exacta, ...$words];
                        $busqueda_secundaria  = $words_primary;
                    } else {
                        $words      = preg_split($h->getTokenizer()->getPattern(), $words_primary, -1, PREG_SPLIT_NO_EMPTY);
                        $busqueda_primaria =  [trim(StrEx::removerAcentos(mb_strtolower($busqueda_original_limpia))), ...$words];
                        $busqueda_secundaria = $words_secondary;
                    }

                    if (!$extraeTodos) {
                        // extraemos la parte más relevante
                        // $parteRelevante = $h->extractRelevant($busqueda_primaria, $textoLimpio, SEARCH_RESULTS_FRAGMENT_SIZE);
                        // $item->descripcion = $h->highlightPonderated($parteRelevante, $busqueda_primaria, $words_secondary, "em", $options);
                         // extraemos todos los extractos que contienen algo del texto buscado
                         $extractos = $h->extractRelevantAll($busqueda_primaria, $textoLimpio, SEARCH_RESULTS_FRAGMENT_SIZE);
                        // dd($extractos);
                         // ordena los extractos en función de la relevancia de la búsqueda
                         self::ordenarExtractos($extractos, $busqueda_original_limpia);

                         if(count($extractos))
                            $item->descripcion = $h->highlightPonderated($extractos[0], $busqueda_primaria, $words_secondary, "em", $options);

                    } else {
                        // extraemos todos los extractos que contienen algo del texto buscado
                        $extractos = $h->extractRelevantAll($busqueda_primaria, $textoLimpio, SEARCH_RESULTS_FRAGMENT_SIZE);

                        // ordena los extractos en función de la relevancia de la búsqueda
                        self::ordenarExtractos($extractos, $busqueda);

                        // remarca las palabras de búsqueda
                        foreach ($extractos as $idx => $extracto) {
                            $extractos[$idx] = $h->highlightPonderated(
                                $extracto,
                                $busqueda_primaria,
                                $busqueda_secundaria,
                                "em",
                                $options
                            );
                        } //

                        $item->extractos = $extractos;
                    }
                }

                // Realizar el mismo proceso para el campo 'titulo'
                if ($item->titulo)
                    $item->titulo = $h->highlightPonderated($item->titulo, $busqueda_primaria, $busqueda_secundaria, "em", $options);
                if ($item->nombre)
                    $item->nombre = $h->highlightPonderated($item->nombre, $busqueda_primaria, $busqueda_secundaria, "em", $options);

                unset($item['texto']);
                unset($item['texto_busqueda']);
                // unset($item['visibilidad']);
                return $item;
            });
    }



    public static function ordenarExtractos(&$extractos, $frase)
    {
        usort($extractos, function ($a, $b) use($frase) {
            // mira si aparece la frase de búsqueda tal cual en los extractos, y prioriza estos
            $textoA = StrEx::removerAcentos(mb_strtolower($a));
            $textoB = StrEx::removerAcentos(mb_strtolower($b));
           $exactoA = strpos($textoA, $frase) !== FALSE ? -1 : 0;
            $exactoB = strpos($textoB, $frase) !== FALSE ? -1 : 0;
            return $exactoA - $exactoB;
        });
    }


    public static function marcarPalabrasDeBusqueda($texto, $busqueda) {

        // hacemos lo mismo que en formatearResultados, pero aplicado solo al texto
        $options = ['tagOptions' => ['class' => 'search-term']];

        $h = new ExtendedHighlighter();

        $busqueda_original_limpia = StrEx::sanitizeAndDeaccent(mb_strtolower($busqueda));

        $busqueda = trim(StrEx::sanitizeAndDeaccent(mb_strtolower($busqueda)));

        list($frase_exacta, $busqueda_sin_frase) = self::obtenerFraseExacta($busqueda);
        if ($frase_exacta)
            $busqueda = $busqueda_sin_frase;

        list($words_primary, $words_secondary) = self::separarPalabrasComunes($busqueda);

        if ($frase_exacta) {
            $words      = preg_split($h->getTokenizer()->getPattern(), $words_primary, -1, PREG_SPLIT_NO_EMPTY);
            $busqueda_primaria = [$frase_exacta, ...$words];
            $busqueda_secundaria  = $words_primary;
        } else {
            $words      = preg_split($h->getTokenizer()->getPattern(), $words_primary, -1, PREG_SPLIT_NO_EMPTY);
            $busqueda_primaria =  [trim(StrEx::removerAcentos(mb_strtolower($busqueda_original_limpia))), ...$words];
            $busqueda_secundaria = $words_secondary;
        }

        return $h->highlightPonderated($texto, $busqueda_primaria, $busqueda_secundaria, "em", $options);
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
                // unset($item['visibilidad']);
                return $item;
            });
    }


    /**
     *
     */
    public static function buscarContenidos($buscar, $coleccion = null)
    {
        $buscar = StrEx::removerAcentos(mb_strtolower($buscar));

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
        $busqueda = $querySearch ?? $queryCheck;
        $busqueda = StrEx::removerAcentos(mb_strtolower($busqueda));
        \App\Models\Comunicado::search($busqueda);
        return BusquedasHelper::validarBusqueda($queryCheck) ? $model::search($busqueda) : $model::whereRaw("1=0");
    }


    /**
     * Indica si es una búsqueda de frase exacta y extrae la frase
     * @return "" si no hay frase exacta
     */
    public static function obtenerFraseExacta($busqueda): array
    {
        // si $busqueda contiene una frase entre comillas dobles, la tomamos como la frase exacta y la retornamos, en caso contrario, devolvermos null
        preg_match('/"([^"]+)"/', $busqueda, $matches);
        if (count($matches) > 1) {
            $frase_exacta = $matches[1];
            $busqueda_sin_frase = trim(str_replace($matches[0], "", $busqueda));
            return [$frase_exacta, $busqueda_sin_frase];
        } else
            return ["", $busqueda];
    }
}
