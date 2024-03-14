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



        // formatea los enlaces:

        // Expresión regular para encontrar URLs con dominios específicos
        $patron = '/(?<!\!)\b(?:https?:\/\/)?(?:www\.)?(tseyor\.(org|com))\b/i';

        // Reemplazar las URLs encontradas por enlaces clicables si no están en formato Markdown
        $html = preg_replace_callback($patron, function ($match) {
            // Verificar si la URL no está ya en formato Markdown
            if (!preg_match('/\[(.*?)\]\((.*?)\)/', $match[0])) {
                $url = (strpos($match[0], 'http') === 0) ? $match[0] : 'http://' . $match[0];
                return '<a target="_blank" href="' . $url . '">' . $match[0] . '</a>';
            } else {
                return $match[0]; // Mantener la URL en formato Markdown
            }
        }, $html);


        // Eliminar espacios sobrantes y saltos de línea
        // $html = preg_replace('/<p>\s+<\/p>\n?/', '', $html);
        // $html = str_replace("\n", '', $html);

        return $html;

    }
}
