<?php

namespace App\Pigmalion;

use  TeamTNT\TNTSearch\Support\AbstractTokenizer;
use  TeamTNT\TNTSearch\Support\TokenizerInterface;

class SpanishTokenizer extends AbstractTokenizer implements TokenizerInterface
{
    static protected $pattern = '/[^\p{L}\p{N}\p{Pc}\p{Pd}@]+/u';


    public function tokenize($text, $stopwords = [])
    {
        /* if(!is_string($text))
        {
            $backtrace = debug_backtrace();

            foreach ($backtrace as $i => $call) {
                if ($i === 0) {
                    echo "Llamada actual: {$call['function']}\n";
                } else {
                    echo "Llamada {$i}: {$call['function']} en " . ($call['file'] ?? '')." línea ".($call['line'] ?? '') ."\n";
                }
            }
            die;
        } */
        if (is_array($text)) return $text;
        $text = mb_strtolower($text);
        $text = self::removeAccents($text); // Eliminar acentos, ñ y ç
        $split = preg_split($this->getPattern(), $text, -1, PREG_SPLIT_NO_EMPTY);
        return array_diff($split, $stopwords);
    }

    public static function removeAccents($text)
    {
        $from = ['á','é','í','ó','ú','ü','ñ','ç'];
        $to   = ['a','e','i','o','u','u','n','c'];

        return str_replace($from, $to, $text);
    }
}
