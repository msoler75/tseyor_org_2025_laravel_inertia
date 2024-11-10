<?php

namespace App\Pigmalion;

use  TeamTNT\TNTSearch\Support\AbstractTokenizer;
use  TeamTNT\TNTSearch\Support\TokenizerInterface;

class SpanishTokenizer extends AbstractTokenizer implements TokenizerInterface
{
    static protected $pattern = '/[^\p{L}\p{N}\p{Pc}\p{Pd}@]+/u';


    public function tokenize($text, $stopwords = [])
    {
        if (is_array($text)) return $text;
        $text = mb_strtolower($text);
        $text = StrEx::removerAcentos($text);
        $split = preg_split($this->getPattern(), $text, -1, PREG_SPLIT_NO_EMPTY);
        $stopwords = ['un', 'una', 'de', 'los', 'las', 'el', 'lo', 'las', 'en', 'y', 'del', 'se', 'o', 'u', 'e'];
        return array_diff($split, $stopwords);
    }

}
