<?php


namespace App\Pigmalion;

use Illuminate\Support\Str;

class Markdown
{

    public static function toHtml($md)
    {
        $html = Str::markdown($md);

        // Reemplazar imágenes con atributos
        $html = preg_replace_callback('/(<img[^>]*>){(\w+=[^}]+)}/', function ($matches) {
            $img = $matches[1];
            $attributes = $matches[2];
            preg_match_all('/(\w+)=([^,]+)/', $attributes, $attr_matches, PREG_SET_ORDER);
            $values = [];
            foreach ($attr_matches as $attr_match) {
                $values[] = "{$attr_match[1]}={$attr_match[2]}";
            }
            return str_replace('<img', '<img ' . implode(' ', $values), $img);
        }, $html);


        // Reemplazar párrafos con estilos
        $html = preg_replace('/<p>{style=([^}]*)}/', "<p style='$1'>", $html);

        // Expresión regular para encontrar URLs con dominios específicos
        $patron = '/(<a[^>]+>)?\b(https?:\/\/)?(www\.)?(tseyor\.(?:org|com))\b(\/[\?&A-Za-z\-\=\/0-9\.]*)?(<\/a>)?/i';

        // Reemplazar las URLs encontradas por enlaces clicables si no están en formato html
        $html = preg_replace_callback($patron, function ($match) {
            $path = $match[5] ?? "";
            if ($path == "/")
                $path = "";
            return '<a target="_blank" href="https://tseyor.org' . $path . '">tseyor.org' . $path . '</a>';
        }, $html);

        // Eliminar espacios sobrantes y saltos de línea
        // $html = preg_replace('/<p>\s+<\/p>\n?/', '', $html);
        // $html = str_replace("\n", '', $html);

        // notas al pie
        $html = preg_replace('/\[\^(\d+)\]/', '<sup>$1</sup>', $html); //[^1]

        return $html;

    }
}
