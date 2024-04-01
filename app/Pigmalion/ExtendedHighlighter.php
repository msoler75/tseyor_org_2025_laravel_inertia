<?php

namespace App\Pigmalion;


use TeamTNT\TNTSearch\Support\Highlighter;

class ExtendedHighlighter extends Highlighter
{


    public function extractRelevantAll($words, $fulltext, $rellength = 300, $prevcount = 50, $indicator = '...')
    {
        // descartamos palabras comunes y masivas
        $words = BusquedasHelper::descartarPalabrasComunes($words);

        // quitamos la palabra "LA"
        $words = preg_replace("/\bla\b/", "", $words);

        $words = preg_split($this->tokenizer->getPattern(), $words, -1, PREG_SPLIT_NO_EMPTY);
        $textlength = mb_strlen($fulltext);
        if ($textlength <= $rellength) {
            return $fulltext;
        }


        $locations = $this->_extractLocations($words, $fulltext);

        $extracts = [];

        $prevStart = -9999;

        foreach ($locations as $location) {
            $startpos = $this->_determineSnipLocation([$location], $prevcount);

            // we jump if we are too close to previous position
            if($startpos < $prevStart + $rellength ) continue;

            // if we are going to snip too much...
            if ($textlength - $startpos < $rellength) {
                $startpos = $startpos - ($textlength - $startpos) / 2;
            }

            // in case no match is found, reset position for proper math below
            if ($startpos == -1) {
                $startpos = 0;
            }

            $prevStart = $startpos;

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

            $extracts[] = $reltext;
        }
        return $extracts;
    }


      /**
       * Override
       */
    public function _extractLocations($words, $fulltext)
    {
        $fulltext = $this->_normalizeText($fulltext);
        $locations = array();
        foreach ($words as $word) {
            $word = $this->_normalizeText($word);
            $wordlen = mb_strlen($word);
            $loc     = mb_stripos($fulltext, $word);
            while ($loc !== false) {
                $locations[] = $loc;
                $loc         = mb_stripos($fulltext, $word, $loc + $wordlen);
            }
        }
        $locations = array_unique($locations);
        sort($locations);
        // dd($locations);

        return $locations;
    }

    private function _normalizeText(string $str) : string
    {
        // Quitamos los acentos y pasamos a minúsculas
        $str = mb_ereg_replace('[áÁ]', 'a', $str);
        $str = mb_ereg_replace('[éÉ]', 'e', $str);
        $str = mb_ereg_replace('[íÍ]', 'i', $str);
        $str = mb_ereg_replace('[óÓ]', 'o', $str);
        $str = mb_ereg_replace('[úÚ]', 'u', $str);

        return mb_strtolower($str);
    }



    /**
       * Override to accept accents
       */
    public function highlight($text, $needle, $tag = 'em', $options = [])
    {
        $this->options = array_merge($this->options, $options);

        $tagAttributes = '';
        if (count($this->options['tagOptions'])) {
            foreach ($this->options['tagOptions'] as $attr => $value) {
                $tagAttributes .= $attr . '="' . $value . '" ';
            }
            $tagAttributes = ' ' . trim($tagAttributes);
        }

        $highlight = '<' . $tag . $tagAttributes .'>\1</' . $tag . '>';



        $needle    = preg_split($this->tokenizer->getPattern(), $needle, -1, PREG_SPLIT_NO_EMPTY);



        // Select pattern to use
        if ($this->options['simple']) {
            $pattern    = '#(%s)#';
            $sl_pattern = '#(%s)#';
        } else {
            $pattern    = '#(?!<.*?)(%s)(?![^<>]*?>)#';
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

            $needle_s = mb_ereg_replace('[aáÁ]', '[aáÁ]', $needle_s);
            $needle_s = mb_ereg_replace('[eéÉ]', '[eéÉ]', $needle_s);
            $needle_s = mb_ereg_replace('[iíÍ]', '[iíÍ]', $needle_s);
            $needle_s = mb_ereg_replace('[oóÓ]', '[oóÓ]', $needle_s);
            $needle_s = mb_ereg_replace('[uúÚ]', '[uúÚ]', $needle_s);


            // Escape needle with optional whole word check
            if ($this->options['wholeWord']) {
                $needle_s = '\b' . $needle_s . '\b';
            }

            // Strip links
            if ($this->options['stripLinks']) {
                $sl_regex = sprintf($sl_pattern, $needle_s);
                $text     = preg_replace($sl_regex, '\1', $text);
            }

            $regex = sprintf($pattern, $needle_s);
            $text  = preg_replace($regex, $highlight, $text);
        }

        return $text;
    }
}
