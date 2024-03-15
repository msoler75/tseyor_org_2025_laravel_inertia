<?php


namespace App\Pigmalion;

use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;

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


    /**
     * Carga un archivo word y extrae el  en formato markdown
     * @param string $docx es el archivo .docx que queremos convertir a markdown
     **/
    public static function fromDocx($docx, $carpetaImagenes = null)
    {

        // generar una carpeta aleatoria
        if(!$carpetaImagenes)
            $carpetaImagenes = 'temp/' . Str::random(16);

        // Settings::setZipClass(Settings::PCLZIP);

        // Cargar el documento Word
        $phpWord = IOFactory::load($docx);

        // Inicializar variables para almacenar texto y rutas de imágenes
        $texto = '';
        $imagenes = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $elementBase) {
                foreach ($elementBase->getElements() as $element) {
                    // dd($element);
                    if (method_exists($element, 'getText')) {
                        $texto .= $element->getText();
                    }
                    if (method_exists($element, 'getMediaId')) {
                        // Guardar la imagen en el disco público de Laravel
                        $imagenPath = $carpetaImagenes . '/' . $element->getTarget(); // Ruta en el disco público
                        Storage::disk('public')->put($imagenPath, $element->getImageString());

                        // Obtener la URL pública de la imagen
                        $imagenUrl = Storage::disk('public')->url($imagenPath);

                        $imagenes[] = $imagenPath;
                        // Insertar la imagen en formato Markdown en el texto
                        $texto .= "\n![](" . $imagenUrl . ")\n";
                    }
                }
            }
        }

        // Convertir el texto a Markdown
        $markdown = strip_tags($texto, "span"); // Eliminar etiquetas HTML
        $markdown = str_replace("\n", "  \n", $markdown); // Agregar doble espacio al final de cada línea

        return $markdown;
    }
}
