<?php

namespace App\Pigmalion;


use TeamTNT\TNTSearch\Support\Highlighter;
use Illuminate\Support\Facades\Log;


define("NEAR_OFFSET", 12);



class ExtendedHighlighter extends Highlighter
{

    public function getTokenizer()
    {
        return $this->tokenizer;
    }

    /**
     * 1/6 ratio on prevcount tends to work pretty well and puts the terms
     * in the middle of the extract
     *
     * @param        $words   Puede ser un Array de words o frases, o un string de palabras que se convertirá a array
     * @param        $fulltext
     * @param int    $rellength
     * @param int    $prevcount
     * @param string $indicator
     *
     * @return bool|string
     */
    public function extractRelevant($words, $fulltext, $rellength = 300, $prevcount = 50, $indicator = '...')
    {
        if (is_string($words))
            $words = preg_split($this->tokenizer->getPattern(), $words, -1, PREG_SPLIT_NO_EMPTY);

        $textlength = mb_strlen($fulltext);
        if ($textlength <= $rellength) {
            return $fulltext;
        }

        $locations = $this->_extractLocations($words, $fulltext);
        $startpos  = $this->_determineSnipLocation($locations, $prevcount);
        // if we are going to snip too much...
        if ($textlength - $startpos < $rellength) {
            $startpos = $startpos - ($textlength - $startpos) / 2;
            $startpos = max($startpos, 0);
        }

        // in case no match is found, reset position for proper math below
        if ($startpos == -1) {
            $startpos = 0;
        }

        $reltext = mb_substr($fulltext, $startpos, $rellength);
        preg_match_all($this->tokenizer->getPattern(), $reltext, $offset, PREG_OFFSET_CAPTURE);
        // since PREG_OFFSET_CAPTURE returns offset in bytes we have to use mb_strlen(substr()) hack here
        $last = mb_strlen(substr($reltext, 0, end($offset[0])[1]));
        $first = mb_strlen(substr($reltext, 0, $offset[0][0][1]));

        // if no match is found, just return first $rellength characters without the last word
        if (empty($locations)) {
            return mb_substr($reltext, 0, $last) . $indicator;
        }

        // check to ensure we dont snip the last word if thats the match
        if ($startpos + $rellength < $textlength) {
            $reltext = mb_substr($reltext, 0, $last) . $indicator; // remove last word
        }

        // If we trimmed from the front add ...
        if ($startpos != 0) {
            $reltext = $indicator . mb_substr($reltext, $first + 1); // remove first word
        }

        return $reltext;
    }


    public function extractRelevantAll(/*$buscar*/$words, $fulltext, $rellength = 300, $prevcount = 50, $indicator = '...')
    {
        $_x = new \App\T("ExtendedHighlighter", "extractRelevantAll");

        $textlength = mb_strlen($fulltext);
        if ($textlength <= $rellength) {
            return [$fulltext];
        }

        // descartamos palabras comunes y masivas
        // list($words, $secundarias) = BusquedasHelper::separarPalabrasComunes($buscar);

        // quitamos la palabra "LA"

        if (is_string($words)) {
            $words = preg_replace("/\bla\b/", "", $words);
            $words = preg_split($this->tokenizer->getPattern(), $words, -1, PREG_SPLIT_NO_EMPTY);
        }
        // dd($secundarias, $words);

        $locations = $this->_extractLocations($words, $fulltext);

        // to-do: if no match is found
        if (empty($locations)) {
            return [];
        }

        $extracts = [];

        $prevStart = -9999;

        foreach ($locations as $location) {
            $startpos = $this->_determineSnipLocation([$location], $prevcount);

            // we jump if we are too close to previous position
            if ($startpos < $prevStart + $rellength)
                continue;

            // if we are going to snip too much...
            if ($textlength - $startpos < $rellength) {
                $startpos = $startpos - ($textlength - $startpos) / 2;
            }

            // in case no match is found, reset position for proper math below
            if ($startpos == -1) {
                $startpos = 0;
            }

            $prevStart = $startpos;

            $_x1 = new \App\T("ExtendedHighlighter", "extractRelevantAll.1");
            $reltext = mb_substr($fulltext, $startpos, $rellength);
            unset($_x1);
            $_x1 = new \App\T("ExtendedHighlighter", "extractRelevantAll.2");
            preg_match_all($this->tokenizer->getPattern(), $reltext, $offset, PREG_OFFSET_CAPTURE);
            unset($_x1);
            // since PREG_OFFSET_CAPTURE returns offset in bytes we have to use mb_strlen(substr()) hack here

            $_x1 = new \App\T("ExtendedHighlighter", "extractRelevantAll.3");

            // to-do: ver por qué a veces no se cumple
            if (is_array($offset[0]) && count($offset[0]) > 0) {
                $last = mb_strlen(substr($reltext, 0, end($offset[0])[1]));
                $first = mb_strlen(substr($reltext, 0, $offset[0][0][1]));

                // check to ensure we dont snip the last word if thats the match
                if ($startpos + $rellength < $textlength) {
                    $reltext = mb_substr($reltext, 0, $last) . $indicator; // remove last word
                }

                // If we trimmed from the front add ...
                if ($startpos != 0) {
                    $reltext = $indicator . mb_substr($reltext, $first + 1); // remove first word
                }
            }
            unset($_x1);

            $extracts[] = $reltext;
        }
        return $extracts;
    }


    /**
     * Override
     */
    public function _extractLocations($words, $fulltext)
    {
        // Log::info("extractLocations: " . implode(" ", $words));
        $_x = new \App\T("ExtendedHighlighter", "_extractLocations");
        $fulltext = StrEx::removerAcentos(mb_strtolower($fulltext));
        // die($fulltext);
        $locations = array();
        // $max_length = mb_strlen($fulltext);
        if (0) {
            foreach ($words as $word) {
                $word = $this->_normalizeText($word);
                $wordlen = mb_strlen($word);
                $loc = mb_stripos($fulltext, $word);
                while ($loc !== false) {
                    $locations[] = $loc;
                    $loc = mb_stripos($fulltext, $word, $loc + $wordlen);
                }
            }
        } else {
            $pattern = '/\b(' . implode('|', $words) . ')\b';
            // $pattern = $word = $this->_normalizeText($pattern);
            /*$pattern = mb_ereg_replace('[aAáÁ]', '[aáÁ]', $pattern);
             $pattern = mb_ereg_replace('[eEéÉ]', '[eéÉ]', $pattern);
             $pattern = mb_ereg_replace('[iIíÍ]', '[iíÍ]', $pattern);
             $pattern = mb_ereg_replace('[oOóÓ]', '[oóÓ]', $pattern);
             $pattern = mb_ereg_replace('[uUúÚ]', '[uúÚ]', $pattern);
             */
            // dd($pattern);
            $pattern .= "/i";
            if (preg_match_all($pattern, $fulltext, $matches, PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $locations[] =  $match[1];
                }
            }
        }

        /*$d = [];
        foreach($locations as $l) {
            $d[]=[$l, mb_substr($fulltext, $l, 50)];
        }
         dd($d);
        */

        $_x2 = new \App\T("ExtendedHighlighter", "_extractLocations_2");

        $locations = array_unique($locations);
        sort($locations);
        // dd($locations);

        return $locations;
    }


    private function _normalizeText(string $str): string
    {
        $_x = new \App\T("ExtendedHighlighter", "_normalizeText");
        // Quitamos los acentos y pasamos a minúsculas
        $str = mb_ereg_replace('[áÁ]', 'a', $str);
        $str = mb_ereg_replace('[éÉ]', 'e', $str);
        $str = mb_ereg_replace('[íÍ]', 'i', $str);
        $str = mb_ereg_replace('[óÓ]', 'o', $str);
        $str = mb_ereg_replace('[úÚ]', 'u', $str);
        $str = mb_ereg_replace('[^A-Za-z0-9]', ' ', $str);

        // hacer todos los reemplazos anteriores en uno solo:
        // $str = str_replace(['á', 'Á']);


        return $str; //mb_strtolower($str);
    }



    /**
     * Override to accept accents
     *
     * @param $needle puede ser un array o un string
     */
    public function highlight($text, $needle, $tag = 'em', $options = [])
    {
        $_x = new \App\T("ExtendedHighlighter", "highlight");

        $this->options = array_merge($this->options, $options);

        $tagAttributes = '';
        if (count($this->options['tagOptions'])) {
            foreach ($this->options['tagOptions'] as $attr => $value) {
                $tagAttributes .= $attr . '="' . $value . '" ';
            }
            $tagAttributes = ' ' . trim($tagAttributes);
        }

        $highlight = '<' . $tag . $tagAttributes . '>\1</' . $tag . '>';


        if (is_string($needle))
            $needle = preg_split($this->tokenizer->getPattern(), $needle, -1, PREG_SPLIT_NO_EMPTY);


        // Select pattern to use
        if ($this->options['simple']) {
            $pattern = '#(%s)#';
            $sl_pattern = '#(%s)#';
        } else {
            $pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#';
            $sl_pattern = '#<a\s(?:.*?)>(%s)</a>#';
        }

        // Add Forgotten Unicode
        $pattern .= 'u';

        // Case sensitivity
        if (!($this->options['caseSensitive'])) {
            $pattern .= 'i';
            $sl_pattern .= 'i';
        }

        $needle = (array) $needle;
        foreach ($needle as $needle_s) {
            $needle_s = preg_quote($needle_s);

            /*$needle_s = mb_ereg_replace('[aáÁ]', '[aáÁ]', $needle_s);
            $needle_s = mb_ereg_replace('[eéÉ]', '[eéÉ]', $needle_s);
            $needle_s = mb_ereg_replace('[iíÍ]', '[iíÍ]', $needle_s);
            $needle_s = mb_ereg_replace('[oóÓ]', '[oóÓ]', $needle_s);
            $needle_s = mb_ereg_replace('[uúÚ]', '[uúÚ]', $needle_s);*/

            $needle_s = preg_replace(['/[aáÁ]/u', '/[eéÉ]/u', '/[iíÍ]/u', '/[oóÓ]/u', '/[uúÚ]/u'], ['[aáá]', '[eéé]', '[iíí]', '[oóó]', '[uúú]'], $needle_s);


            // Escape needle with optional whole word check
            if ($this->options['wholeWord']) {
                $needle_s = '\b' . $needle_s . '\b';
            }

            // Strip links
            if ($this->options['stripLinks']) {
                $sl_regex = sprintf($sl_pattern, $needle_s);
                $text = preg_replace($sl_regex, '\1', $text);
            }

            $regex = sprintf($pattern, $needle_s);
            $text = preg_replace($regex, $highlight, $text);
        }

        return $text;
    }




    /**
     * Only if words are near to other highlighted words
     *
     * @param $needle puede ser un array o un string
     */
    public function highlightNear($text, $needle, $tag = 'em', $options = [])
    {

        $_x = new \App\T("ExtendedHighlighter", "highlightNear");

        $this->options = array_merge($this->options, $options);

        $tagAttributes = '';
        if (count($this->options['tagOptions'])) {
            foreach ($this->options['tagOptions'] as $attr => $value) {
                $tagAttributes .= $attr . '="' . $value . '" ';
            }
            $tagAttributes = ' ' . trim($tagAttributes);
        }

        $highlight = '<' . $tag . $tagAttributes . '>\1</' . $tag . '>';

        if (is_string($needle))
            $needle = preg_split($this->tokenizer->getPattern(), $needle, -1, PREG_SPLIT_NO_EMPTY);


        // Select pattern to use
        if ($this->options['simple']) {
            $pattern = '#(%s)#';
            $sl_pattern = '#(%s)#';
        } else {
            $pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#';
            $sl_pattern = '#<a\s(?:.*?)>(%s)</a>#';
        }

        // Add Forgotten Unicode
        $pattern .= 'u';

        // Case sensitivity
        if (!($this->options['caseSensitive'])) {
            $pattern .= 'i';
            $sl_pattern .= 'i';
        }

        $needle = (array) $needle;

        foreach ($needle as $needle_s) {
            //$needle_s = preg_quote($needle_s);

            $_x2 = new \App\T("ExtendedHighlighter", "highlightNear.regex_needle");
            /*$needle_s = mb_ereg_replace('[aáÁ]', '[aáÁ]', $needle_s);
            $needle_s = mb_ereg_replace('[eéÉ]', '[eéÉ]', $needle_s);
            $needle_s = mb_ereg_replace('[iíÍ]', '[iíÍ]', $needle_s);
            $needle_s = mb_ereg_replace('[oóÓ]', '[oóÓ]', $needle_s);
            $needle_s = mb_ereg_replace('[uúÚ]', '[uúÚ]', $needle_s);*/

            $needle_s = preg_replace(['/[aáÁ]/u', '/[eéÉ]/u', '/[iíÍ]/u', '/[oóÓ]/u', '/[uúÚ]/u'], ['[aáá]', '[eéé]', '[iíí]', '[oóó]', '[uúú]'], $needle_s);
            unset($_x2);


            // Escape needle with optional whole word check
            if ($this->options['wholeWord']) {
                $needle_s = '\b' . $needle_s . '\b';
            }

            // Strip links
            if ($this->options['stripLinks']) {
                $sl_regex = sprintf($sl_pattern, $needle_s);
                $text = preg_replace($sl_regex, '\1', $text);
            }

            $regex = sprintf($pattern, $needle_s);
            // dd($regex);
            $text = preg_replace_callback($regex, function ($match) use ($text, $tag, $highlight) {
                $word = $match[0][0];
                $pos = $match[0][1];
                // miramos si desde la posición $pos, 12 unidades hacia delante o 12 hacia atrás hay alguna etiqueta <em
                // si la hay, es válida la coincidencia
                $frag = substr($text, max(0, $pos - NEAR_OFFSET), strlen($word) + NEAR_OFFSET * 2);
                if (preg_match("#</?$tag#i", $frag))
                    return preg_replace("#(.*)#", $highlight, $word);
                return $match[0][0];
            }, $text, -1, $count, PREG_OFFSET_CAPTURE);
        }

        return $text;
    }


    public function highlightPonderated($text, $words_relevant, $words_irrelevant, $tag = 'em', $options = [])
    {
        $_x = new \App\T("ExtendedHighlighter", "highlightPonderated");
        $result = $this->highlight($text, $words_relevant, $tag, $options);
        if ($words_irrelevant)
            $result = $this->highlightNear($result, $words_irrelevant, $tag, $options);
        return $result;
    }
}
