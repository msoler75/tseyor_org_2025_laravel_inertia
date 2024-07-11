<?php

namespace App\Pigmalion;

use Illuminate\Support\Str;

class StrEx extends Str
{

    public static function removerAcentosIconv($texto)
    {
        $texto = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $texto);
        return $texto;
    }

    public static function removerAcentosStrtr($texto)
    {
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
            'Ú' => 'U',
            //'Ñ'=>'N',
            //'ñ'=> 'n'
            // Agrega más caracteres acentuados y sus correspondientes sin acento si es necesario
        );
        $texto = strtr($texto, $acentos);
        return $texto;
    }


    public static function removerAcentosRegex1($texto)
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

    public static function removerAcentosRegex2($string)
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

    public static function removerAcentosEreg($str)
    {
        $str = mb_ereg_replace('[áÁ]', 'a', $str);
        $str = mb_ereg_replace('[éÉ]', 'e', $str);
        $str = mb_ereg_replace('[íÍ]', 'i', $str);
        $str = mb_ereg_replace('[óÓ]', 'o', $str);
        $str = mb_ereg_replace('[úÚ]', 'u', $str);
        // $str = mb_ereg_replace('[^A-Za-z0-9]', ' ', $str);
        return $str;
    }

    public static function sanitizeAndDeaccent($str)
    {
        $str = self::removerAcentosStrtr($str);
        $str = preg_replace('/[^A-Za-z0-9ñÑçÇ]/u', ' ', $str);
        return $str;
    }


    /**
     * Retorna un porcentaje de similitud
     */
    public static function compareFuzzy($str1, $str2)
    {
        $str1 = strtolower(self::sanitizeAndDeaccent($str1));
        $str2 = strtolower(self::sanitizeAndDeaccent($str2));

        // Calcula la distancia de Levenshtein entre las dos cadenas
        $levenshteinDistance = levenshtein($str1, $str2);



        // Calcula la longitud de la cadena más larga entre las dos
        $maxLen = max(strlen($str1), strlen($str2));

        // Si la longitud máxima es 0 (ambas cadenas están vacías), devuelve 100% de similitud
        if ($maxLen == 0) {
            return 100;
        }

        // Calcula el porcentaje de similitud
        $similarity = (1 - ($levenshteinDistance / $maxLen)) * 100;

        // Devuelve el porcentaje de similitud
        return round($similarity, 2);
    }
}
