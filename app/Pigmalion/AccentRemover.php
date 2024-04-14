<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Log;


function removerAcentosIconv($texto)
{
    $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
    return $texto;
}

function removerAcentosStrtr($texto)
{
    $_x = new \App\T("AccentRemover", "removerAcentosStrtr");
    $acentos = array(
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'Á' => 'A',
        'É' => 'E',
        'Í' => 'I',
        'Ó' => 'O',
        'Ú' => 'U'
        // Agrega más caracteres acentuados y sus correspondientes sin acento si es necesario
    );
    $texto = strtr($texto, $acentos);
    return $texto;
}


function removerAcentosRegex1($texto)
{
    $acentos = array(
        '/[áàâãªä]/u' => 'a',
        '/[ÁÀÂÃÄ]/u' => 'A',
        '/[éèêë]/u' => 'e',
        '/[ÉÈÊË]/u' => 'E',
        '/[íìîï]/u' => 'i',
        '/[ÍÌÎÏ]/u' => 'I',
        '/[óòôõºö]/u' => 'o',
        '/[ÓÒÔÕÖ]/u' => 'O',
        '/[úùûü]/u' => 'u',
        '/[ÚÙÛÜ]/u' => 'U',
        '/ç/' => 'c',
        '/Ç/' => 'C'
        // Agrega más caracteres acentuados y sus correspondientes sin acento si es necesario
    );
    $texto = preg_replace(array_keys($acentos), array_values($acentos), $texto);
    return $texto;
}

function removerAcentosRegex2($string)
{
    $string = preg_replace_callback(
        '/[\x{00C0}-\x{017F}]/u',
        function ($matches) {
            $char = $matches[0];
            $accented = htmlentities($char, ENT_QUOTES, 'UTF-8');
            $unaccented = html_entity_decode(preg_replace('/&([a-zA-Z])(uml|acute|grave|circ|tilde|cedil);/', '$1', $accented), ENT_QUOTES, 'UTF-8');
            return $unaccented;
        },
        $string
    );
    return $string;
}

function removerAcentosEreg($str)
{
    $str = mb_ereg_replace('[áÁ]', 'a', $str);
    $str = mb_ereg_replace('[éÉ]', 'e', $str);
    $str = mb_ereg_replace('[íÍ]', 'i', $str);
    $str = mb_ereg_replace('[óÓ]', 'o', $str);
    $str = mb_ereg_replace('[úÚ]', 'u', $str);
    // $str = mb_ereg_replace('[^A-Za-z0-9]', ' ', $str);
    return $str;
}

class AccentRemover
{


    public static function removeNonAscii($str)
    {
        $_x = new \App\T("AccentRemover", "removeNonAscii");
        /*$str = mb_ereg_replace('[áÁ]', 'a', $str);
        $str = mb_ereg_replace('[éÉ]', 'e', $str);
        $str = mb_ereg_replace('[íÍ]', 'i', $str);
        $str = mb_ereg_replace('[óÓ]', 'o', $str);
        $str = mb_ereg_replace('[úÚ]', 'u', $str);*/

        $str = removerAcentosStrtr($str);
        $str = preg_replace('/[^A-Za-z0-9]/u', ' ', $str);
        return $str;
    }

    public static function benchmark($texto)
    {




        $reps = 100;


        // Función 1: removerAcentosIconv
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            removerAcentosIconv($texto);
        }
        $end = microtime(true);
        $time1 = $end - $start;

        // Función 2: removerAcentosStrtr
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            removerAcentosStrtr($texto);
        }
        $end = microtime(true);
        $time2 = $end - $start;

        // Función 3: removerAcentosRegex
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            removerAcentosRegex1($texto);
        }
        $end = microtime(true);
        $time3 = $end - $start;

        // Función 3: removerAcentosRegex
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            removerAcentosRegex2($texto);
        }
        $end = microtime(true);
        $time4 = $end - $start;


        // Función 4: removerAcentosEreg
        $start = microtime(true);
        for ($i = 0; $i < $reps; $i++) {
            removerAcentosEreg($texto);
        }
        $end = microtime(true);
        $time5 = $end - $start;

        // Resultados
        Log::info("Tiempo removerAcentosIconv: " . $time1 . " segundos");
        Log::info("Tiempo removerAcentosStrtr: " . $time2 . " segundos");
        Log::info("Tiempo removerAcentosRegx1: " . $time3 . " segundos");
        Log::info("Tiempo removerAcentosRegx2: " . $time4 . " segundos");
        Log::info("Tiempo removerAcentosEreg : " . $time5 . " segundos");


    }

}
