<?php


namespace App\Pigmalion;

use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use League\HTMLToMarkdown\HtmlConverter;


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

        // centramos las imágenes solitarias
        $regex = "/<p>(<img[^>]+>)<\/p>/";
        $html = preg_replace($regex, "<p style='text-align: center'>$1</p>", $html);


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
        if (!$carpetaImagenes)
            $carpetaImagenes = 'temp/' . Str::random(16);

        // Settings::setZipClass(Settings::PCLZIP);

        // Cargar el documento Word
        $phpWord = IOFactory::load($docx);

        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
        // Generate the HTML content
        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
        $htmlContent = $htmlWriter->getContent();

        // Parse the HTML content using DOMDocument
        $dom = new \DOMDocument();
        $dom->loadHTML($htmlContent, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Get the body content without the HTML, HEAD, and BODY tags
        $bodyContent = '';
        $bodyNodes = $dom->getElementsByTagName('body')->item(0)->childNodes;
        foreach ($bodyNodes as $node) {
            // extraemos los div de pagina en el primer nivel del body:
            // <div style="page: page1">
            if ($node->nodeName == 'div' && preg_match('/page\d+/', $node->getAttribute('style'))) {
                $nodes = $node->childNodes;
                foreach ($nodes as $node2) {
                    $bodyContent .= $dom->saveHTML($node2);
                }
            } else
                $bodyContent .= $dom->saveHTML($node);
        }

        $converter = new HtmlConverter();
        $markdown = $converter->convert($bodyContent);

        $markdown = preg_replace('/<div style="page: page\d+">/', '', $markdown);
        // extraemos las imagenes (codificadas en base64) y las guardamos en disco

        // Expresión regular para encontrar imágenes codificadas en base64 en el texto Markdown
        $pattern = '/!\[\]\(data:image\/([a-zA-Z]*);base64,([^)]*)\)/';

        // Obtener todas las coincidencias de imágenes codificadas en base64
        preg_match_all($pattern, $markdown, $matches);

        foreach ($matches[0] as $key => $match) {
            // Obtener el tipo de imagen y los datos base64
            $type = $matches[1][$key];
            $data = $matches[2][$key];

            // Decodificar los datos base64 y guardar la imagen en disco
            $imageData = base64_decode($data);
            $imageName = 'image_' . $key . '.' . $type;
            $imagePath = $carpetaImagenes . '/' . $imageName;

            // Guardar la imagen en disco público
            Storage::disk('public')->put($imagePath, $imageData);

            // Obtener la URL pública de la imagen guardada
            $imageUrl = Storage::disk('public')->url($imagePath);

            // Reemplazar el enlace de la imagen codificada por la URL pública de la imagen guardada
            $markdown = str_replace($match, "![](" . $imageUrl . ")", $markdown);
        }


        return $markdown;

        // Inicializar variables para almacenar texto y rutas de imágenes
        $texto = '';
        function processElement($element, &$texto, $carpetaImagenes)
        {
            if (method_exists($element, 'getElements')) {
                foreach ($element->getElements() as $element2) {
                    processElement($element2, $texto, $carpetaImagenes);
                }
            } else {
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

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                processElement($element, $texto, $carpetaImagenes);
            }
        }

        // Convertir el texto a Markdown
        $markdown = strip_tags($texto, "span"); // Eliminar etiquetas HTML
        $markdown = str_replace("\n", "  \n", $markdown); // Agregar doble espacio al final de cada línea

        return $markdown;
    }
}
