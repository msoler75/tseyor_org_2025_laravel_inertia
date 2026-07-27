<?php

namespace App\Helpers;

class DiffRenderer
{
    public static function render(string $old, string $new): string
    {
        $old = (string) $old;
        $new = (string) $new;

        if ($old === $new) {
            return '<div style="color:#999;font-style:italic">Sin cambios</div>';
        }

        $old = str_replace(["\r\n", "\r"], "\n", $old);
        $new = str_replace(["\r\n", "\r"], "\n", $new);

        if ($old === '' || $old === null) {
            return '<pre style="color:#22863a;background:#e6ffe6;padding:8px;border-radius:4px;border-left:3px solid #2d8a2d;white-space:pre-wrap;word-break:break-word;font-family:monospace;font-size:0.875rem;margin:0">'
                . e($new) . '</pre>';
        }

        if ($new === '' || $new === null) {
            return '<pre style="color:#cb2431;background:#ffe6e6;padding:8px;border-radius:4px;border-left:3px solid #c42b2b;white-space:pre-wrap;word-break:break-word;font-family:monospace;font-size:0.875rem;margin:0">'
                . e($old) . '</pre>';
        }

        $oldLines = explode("\n", $old);
        $newLines = explode("\n", $new);

        if (count($oldLines) === 1 && count($newLines) === 1) {
            return self::renderInlineWordDiff($old, $new);
        }

        $differ = new \SebastianBergmann\Diff\Differ(
            new \SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder("")
        );

        $diffArray = $differ->diffToArray($oldLines, $newLines);

        return self::renderLineGroupedDiff($diffArray);
    }

    private static function stripFormatting(string $text): string
    {
        $text = preg_replace('/\{[^}]*\}/', '', $text);
        $text = strip_tags($text);
        $text = str_replace(['*', '_', '\\'], '', $text);
        return $text;
    }

    private static function renderInlineWordDiff(string $old, string $new): string
    {
        $oldStripped = self::stripFormatting($old);
        $newStripped = self::stripFormatting($new);

        if ($oldStripped === $newStripped && $oldStripped !== '') {
            $normOld = str_replace('_', '*', $old);
            $normNew = str_replace('_', '*', $new);
            if (str_contains($normNew, $normOld)) {
                $pos = mb_strpos($normNew, $normOld);
                return e(mb_substr($new, 0, $pos))
                    . self::renderDelimiterChange($old, mb_substr($new, $pos, mb_strlen($old)))
                    . e(mb_substr($new, $pos + mb_strlen($old)));
            }
            if (str_contains($normOld, $normNew)) {
                $pos = mb_strpos($normOld, $normNew);
                return e(mb_substr($old, 0, $pos))
                    . self::renderDelimiterChange(mb_substr($old, $pos, mb_strlen($new)), $new)
                    . e(mb_substr($old, $pos + mb_strlen($new)));
            }
            if (str_contains($new, $old)) {
                $pos = mb_strpos($new, $old);
                return e(mb_substr($new, 0, $pos)) . e($old) . e(mb_substr($new, $pos + mb_strlen($old)));
            }
            if (str_contains($old, $new)) {
                $pos = mb_strpos($old, $new);
                return e(mb_substr($old, 0, $pos)) . e($new) . e(mb_substr($old, $pos + mb_strlen($new)));
            }
            if (self::canUseCharDiff($old, $new)) {
                return self::renderDelimiterChange($old, $new);
            }
        }

        if (self::similarityScore($old, $new) < 0.3) {
            return '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e($old) . '</del>'
                . '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e($new) . '</ins>';
        }

        if (self::similarityScore($old, $new) > 0.70) {
            return self::renderCharDiff($old, $new);
        }

        $oldWords = self::splitWords($old);
        $newWords = self::splitWords($new);

        $differ = new \SebastianBergmann\Diff\Differ(
            new \SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder("")
        );

        $diffArray = $differ->diffToArray($oldWords, $newWords);

        $html = '<div style="font-family:monospace;font-size:0.875rem;line-height:1.6;background:#f8f9fa;padding:12px;border-radius:4px;color:#24292e;word-break:break-word">';

        foreach ($diffArray as $entry) {
            $text = $entry[0];
            $type = $entry[1];

            if ($type === \SebastianBergmann\Diff\Differ::REMOVED) {
                $html .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e(self::vis($text)) . '</del>';
            } elseif ($type === \SebastianBergmann\Diff\Differ::ADDED) {
                $html .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e(self::vis($text)) . '</ins>';
            } else {
                $html .= e($text);
            }
        }

        $html .= '</div>';

        return $html;
    }

    private static function renderLineGroupedDiff(array $diffArray): string
    {
        $contextWindow = 2;
        $result = [];
        $total = count($diffArray);
        $lastShown = -999;

        for ($i = 0; $i < $total; $i++) {
            $type = $diffArray[$i][1];
            $isChange = $type === \SebastianBergmann\Diff\Differ::REMOVED
                     || $type === \SebastianBergmann\Diff\Differ::ADDED;

            if ($isChange) {
                $from = max(0, $i - $contextWindow);
                $to = min($total - 1, $i + $contextWindow);

                for ($j = $from; $j <= $to; $j++) {
                    if ($j > $lastShown) {
                        $result[] = $j;
                        $lastShown = $j;
                    }
                }
            }
        }

        $html = '<pre style="color:#24292e;font-family:monospace;font-size:0.875rem;line-height:1.5;background:#f8f9fa;padding:8px;border-radius:4px;overflow-x:auto;white-space:pre-wrap;word-break:break-word;margin:0">';

        $i = 0;
        $count = count($result);

        while ($i < $count) {
            $idx = $result[$i];
            $type = $diffArray[$idx][1];
            $isChange = $type === \SebastianBergmann\Diff\Differ::REMOVED
                     || $type === \SebastianBergmann\Diff\Differ::ADDED;

            if (! $isChange) {
                $text = trim($diffArray[$idx][0]);
                $isBlankContext = $text === '';

                if (! $isBlankContext && $i > 0 && $idx > $result[$i - 1] + 1) {
                    $html .= '<span style="color:#6a737d">...</span><br>';
                }
                if (! $isBlankContext) {
                    $html .= '<span style="color:#6a737d">  ' . e(rtrim($diffArray[$idx][0])) . '</span><br>';
                }
                $i++;
                continue;
            }

            if ($i > 0) {
                $prevIdx = $result[$i - 1];
                $prevType = $diffArray[$prevIdx][1];
                $prevIsChange = $prevType === \SebastianBergmann\Diff\Differ::REMOVED
                             || $prevType === \SebastianBergmann\Diff\Differ::ADDED;
                if (! $prevIsChange && $prevIdx < $idx - 1) {
                    $html .= '<span style="color:#6a737d">...</span><br>';
                }
            }

            $removedRaw = [];
            $addedRaw = [];

            while ($i < $count) {
                $idx = $result[$i];
                $t = $diffArray[$idx][1];
                $raw = $diffArray[$idx][0];
                if ($t === \SebastianBergmann\Diff\Differ::REMOVED) {
                    $removedRaw[] = $raw;
                    $i++;
                } elseif ($t === \SebastianBergmann\Diff\Differ::ADDED) {
                    $addedRaw[] = $raw;
                    $i++;
                } elseif (trim($raw) === '') {
                    $i++;
                } else {
                    break;
                }
            }

            $paired = self::pairLinesBySimilarity($removedRaw, $addedRaw);
            $outputRemoved = [];
            $outputAdded = [];

            foreach ($paired as $pair) {
                if ($pair[0] !== null && $pair[1] !== null) {
                    $html .= '<span style="color:#24292e">  ' . self::renderPairedWordDiff($pair[0], $pair[1]) . '</span><br>';
                } elseif ($pair[0] !== null) {
                    $outputRemoved[] = e(self::vis(rtrim($pair[0])));
                } else {
                    $outputAdded[] = e(self::vis(rtrim($pair[1])));
                }
            }

            if (! empty($outputRemoved)) {
                foreach ($outputRemoved as $line) {
                    $html .= '<span style="background:#ffeef0;color:#cb2431">- ' . $line . '</span><br>';
                }
            }
            if (! empty($outputAdded)) {
                foreach ($outputAdded as $line) {
                    $html .= '<span style="background:#e6ffed;color:#22863a">+ ' . $line . '</span><br>';
                }
            }
        }

        $html .= '</pre>';

        return $html;
    }

    private static function pairLinesBySimilarity(array $removed, array $added): array
    {
        $usedAdded = array_fill(0, count($added), false);
        $pairs = [];

        foreach ($removed as $r) {
            $bestIdx = null;
            $bestScore = 0;

            foreach ($added as $aIdx => $a) {
                if ($usedAdded[$aIdx]) {
                    continue;
                }

                $score = self::similarityScore($r, $a);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestIdx = $aIdx;
                }
            }

            if ($bestIdx !== null && $bestScore > 0.4) {
                $pairs[] = [$r, $added[$bestIdx]];
                $usedAdded[$bestIdx] = true;
            } else {
                $pairs[] = [$r, null];
            }
        }

        foreach ($added as $aIdx => $a) {
            if (! $usedAdded[$aIdx]) {
                $pairs[] = [null, $a];
            }
        }

        return $pairs;
    }

    private static function similarityScore(string $a, string $b): float
    {
        if ($a === $b) {
            return 1.0;
        }

        if (str_contains($b, $a) || str_contains($a, $b)) {
            $shorter = min(mb_strlen($a), mb_strlen($b));
            $longer = max(mb_strlen($a), mb_strlen($b));
            return 0.6 + (0.4 * $shorter / $longer);
        }

        $maxLen = max(1, max(mb_strlen($a), mb_strlen($b)));
        $lev = levenshtein($a, $b);

        return max(0, 1 - ($lev / $maxLen));
    }

    private static function renderPairedWordDiff(string $oldLine, string $newLine): string
    {
        $mdOld = self::parseMarkdownLink($oldLine);
        $mdNew = self::parseMarkdownLink($newLine);

        if ($mdOld !== null && $mdNew !== null) {
            $out = '';
            foreach (['bang', 'alt', 'urlOpen', 'url', 'urlClose'] as $part) {
                $o = $mdOld[$part];
                $n = $mdNew[$part];
                if ($o === $n) {
                    $out .= e($o);
                } elseif ($part === 'url') {
                    $out .= self::renderUrlDiff($o, $n);
                } else {
                    $pair = self::similarityScore($o, $n) > 0.85
                        ? self::renderCharDiff($o, $n)
                        : '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e(self::vis($o)) . '</del>'
                        . '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e(self::vis($n)) . '</ins>';
                    $out .= $pair;
                }
            }
            return $out;
        }

        $oldStripped = self::stripFormatting($oldLine);
        $newStripped = self::stripFormatting($newLine);

        if ($oldStripped === $newStripped && $oldStripped !== '') {
            if (str_contains($newLine, $oldLine)) {
                $pos = mb_strpos($newLine, $oldLine);
                return e(mb_substr($newLine, 0, $pos))
                    . e($oldLine)
                    . e(mb_substr($newLine, $pos + mb_strlen($oldLine)));
            }
            if (str_contains($oldLine, $newLine)) {
                $pos = mb_strpos($oldLine, $newLine);
                return e(mb_substr($oldLine, 0, $pos))
                    . e($newLine)
                    . e(mb_substr($oldLine, $pos + mb_strlen($newLine)));
            }
            $normOld = str_replace('_', '*', $oldLine);
            $normNew = str_replace('_', '*', $newLine);
            if (str_contains($normNew, $normOld)) {
                $pos = mb_strpos($normNew, $normOld);
                $before = mb_substr($newLine, 0, $pos);
                $after = mb_substr($newLine, $pos + mb_strlen($oldLine));
                return e($before) . self::renderDelimiterChange($oldLine, mb_substr($newLine, $pos, mb_strlen($oldLine))) . e($after);
            }
            if (str_contains($normOld, $normNew)) {
                $pos = mb_strpos($normOld, $normNew);
                $before = mb_substr($oldLine, 0, $pos);
                $after = mb_substr($oldLine, $pos + mb_strlen($newLine));
                return e($before) . self::renderDelimiterChange(mb_substr($oldLine, $pos, mb_strlen($newLine)), $newLine) . e($after);
            }
            if (self::canUseCharDiff($oldLine, $newLine)) {
                return self::renderDelimiterChange($oldLine, $newLine);
            }
        }

        if (self::similarityScore($oldLine, $newLine) > 0.70) {
            return self::renderCharDiff($oldLine, $newLine);
        }

        $oldWords = self::splitWords($oldLine);
        $newWords = self::splitWords($newLine);

        $differ = new \SebastianBergmann\Diff\Differ(
            new \SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder("")
        );

        $diffArray = $differ->diffToArray($oldWords, $newWords);

        $out = '';
        $buffer = '';

        foreach ($diffArray as $entry) {
            $text = $entry[0];
            $type = $entry[1];

            if ($type === \SebastianBergmann\Diff\Differ::REMOVED && ($text === '*' || $text === '_')) {
                $buffer .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e(self::vis($text)) . '</del>';
            } elseif ($type === \SebastianBergmann\Diff\Differ::ADDED && ($text === '*' || $text === '_')) {
                if ($buffer !== '') {
                    $buffer .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e(self::vis($text)) . '</ins>';
                    $out .= $buffer;
                    $buffer = '';
                } else {
                    $out .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e(self::vis($text)) . '</ins>';
                }
            } else {
                if ($buffer !== '') {
                    $out .= $buffer;
                    $buffer = '';
                }
                if ($type === \SebastianBergmann\Diff\Differ::REMOVED) {
                    $out .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e(self::vis($text)) . '</del>';
                } elseif ($type === \SebastianBergmann\Diff\Differ::ADDED) {
                    $out .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e(self::vis($text)) . '</ins>';
                } else {
                    $out .= e($text);
                }
            }
        }

        if ($buffer !== '') {
            $out .= $buffer;
        }

        return $out;
    }

    private static function canUseCharDiff(string $old, string $new): bool
    {
        return str_replace(['*', '_'], '', $old) === str_replace(['*', '_'], '', $new)
            && str_replace('_', '*', $old) === str_replace('_', '*', $new);
    }

    private static function splitWords(string $text): array
    {
        return preg_split('/(\s+|[\*\[\]\(\)<>])/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    }

    private static function vis(string $text): string
    {
        return str_replace(' ', '·', $text);
    }

    private static function parseMarkdownLink(string $line): ?array
    {
        if (preg_match('/^(!?\[)(.*?)(\]\()(.*?)(\))$/u', $line, $m)) {
            return [
                'bang'    => $m[1],
                'alt'     => $m[2],
                'urlOpen' => $m[3],
                'url'     => $m[4],
                'urlClose'=> $m[5],
            ];
        }
        return null;
    }

    private static function renderUrlDiff(string $oldUrl, string $newUrl): string
    {
        $oldLen = mb_strlen($oldUrl);
        $newLen = mb_strlen($newUrl);
        $minLen = min($oldLen, $newLen);

        $prefixLen = 0;
        for ($i = 0; $i < $minLen; $i++) {
            if (mb_substr($oldUrl, $i, 1) !== mb_substr($newUrl, $i, 1)) {
                break;
            }
            $prefixLen++;
        }

        $suffixLen = 0;
        for ($i = 1; $i <= $minLen - $prefixLen; $i++) {
            if (mb_substr($oldUrl, $oldLen - $i, 1) !== mb_substr($newUrl, $newLen - $i, 1)) {
                break;
            }
            $suffixLen++;
        }

        $prefix = mb_substr($oldUrl, 0, $prefixLen);
        $oldMid = mb_substr($oldUrl, $prefixLen, $oldLen - $prefixLen - $suffixLen);
        $newMid = mb_substr($newUrl, $prefixLen, $newLen - $prefixLen - $suffixLen);
        $suffix = mb_substr($oldUrl, $oldLen - $suffixLen);

        $out = e($prefix);

        if ($oldMid !== '' || $newMid !== '') {
            if ($oldMid === $newMid) {
                $out .= e($oldMid);
            } elseif ($oldMid !== '' && $newMid !== '') {
                $out .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e($oldMid) . '</del>'
                      . '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e($newMid) . '</ins>';
            } elseif ($oldMid !== '') {
                $out .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e($oldMid) . '</del>';
            } else {
                $out .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e($newMid) . '</ins>';
            }
        }

        $out .= e($suffix);

        return $out;
    }

    private static function renderCharDiff(string $oldLine, string $newLine): string
    {
        $old = mb_str_split($oldLine);
        $new = mb_str_split($newLine);

        $differ = new \SebastianBergmann\Diff\Differ(
            new \SebastianBergmann\Diff\Output\DiffOnlyOutputBuilder("")
        );

        $diffArray = $differ->diffToArray($old, $new);

        $out = '';
        foreach ($diffArray as $entry) {
            $ch = $entry[0];
            $type = $entry[1];

            if ($type === \SebastianBergmann\Diff\Differ::REMOVED) {
                $out .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e(self::vis($ch)) . '</del>';
            } elseif ($type === \SebastianBergmann\Diff\Differ::ADDED) {
                $out .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e(self::vis($ch)) . '</ins>';
            } else {
                $out .= e($ch);
            }
        }

        return $out;
    }

    private static function renderDelimiterChange(string $oldLine, string $newLine): string
    {
        $out = '';
        $len = max(mb_strlen($oldLine), mb_strlen($newLine));

        for ($i = 0; $i < $len; $i++) {
            $oc = mb_substr($oldLine, $i, 1);
            $nc = mb_substr($newLine, $i, 1);

            if ($oc === $nc) {
                $out .= e($oc);
            } elseif (($oc === '*' || $oc === '_') && ($nc === '*' || $nc === '_')) {
                $out .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">' . e($oc) . '</del>'
                      . '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">' . e($nc) . '</ins>';
            } elseif ($oc === '*') {
                $out .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">*</del>';
            } elseif ($oc === '_') {
                $out .= '<del style="background:#ffeef0;color:#cb2431;text-decoration:none">_</del>';
            } elseif ($nc === '*') {
                $out .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">*</ins>';
            } elseif ($nc === '_') {
                $out .= '<ins style="background:#e6ffed;color:#22863a;text-decoration:none">_</ins>';
            } else {
                $out .= e($nc ?: $oc);
            }
        }

        return $out;
    }
}
