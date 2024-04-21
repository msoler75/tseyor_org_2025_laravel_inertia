<?php


namespace App\Pigmalion;

use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;
use Illuminate\Support\Facades\Storage;
use League\HTMLToMarkdown\HtmlConverter;
use Illuminate\Support\Facades\Log;


class Markdown
{
    public static $notasEncontradas = 0;
    public static $imagenesExtraidas = [];
    public static $carpetaCreada = null;


    public static function toHtml($md)
    {
        $html = Str::markdown($md);

        // Reemplazar imágenes con atributos
        $html = preg_replace_callback('/(<img[^>]*>){(\w+=[^}]+)}/', function ($match) {
            $img = $match[1];
            $attributes = $match[2];
            preg_match_all('/(\w+)=([^,]+)/', $attributes, $attr_matches, PREG_SET_ORDER);
            $values = [];
            foreach ($attr_matches as $attr_match) {
                $values[] = "{$attr_match[1]}={$attr_match[2]}";
            }
            return str_replace('<img', '<img ' . implode(' ', $values), $img);
        }, $html);

        // Reemplazar párrafos con estilos
        $html = preg_replace('/<p>{\s*style=([^}]*)\s*}/', "<p style='$1'>", $html);

        // centramos las imágenes solitarias
        $regex = "/<p>(<img[^>]+>)<\/p>/";
        $html = preg_replace($regex, "<p style='text-align: center'>$1</p>", $html);

        // Arreglar enlaces
        // Expresión regular para encontrar URLs con dominios específicos
        $patron = '/(<a[^>]+>)?\b(https?:\/\/)?(www\.)?(tseyor\.(?:org|com))\b(\/[\?&A-Za-z\-\=\/0-9\.]*)?(<\/a>)?/i';
        // Reemplazar las URLs encontradas por enlaces clicables si no están en formato html
        $html = preg_replace_callback($patron, function ($match) {
            $path = $match[5] ?? "";
            if ($path == "/")
                $path = "";
            return '<a target="_blank" href="https://tseyor.org' . $path . '">tseyor.org' . $path . '</a>';
        }, $html);
        // si enlace está partido:
        // $html = preg_replace("$<a href=.*tseyor.</[^>]+>.*(org|com)</.*>$", '<a target="_blank" href="https://tseyor.org">tseyor.org</a>', $html);

        // Eliminar espacios sobrantes y saltos de línea
        // $html = preg_replace('/<p>\s+<\/p>\n?/', '', $html);
        // $html = str_replace("\n", '', $html);

        // dar formato html a notas al pie
        $html = preg_replace('/\[\^(\d+)\]/', '<sup>$1</sup>', $html); //[^1]

        return $html;

    }


    /**
     * Carga un archivo word y extrae el contenido y lo transforma en formato markdown
     * @param string $docx es el archivo .docx que queremos convertir a markdown
     * @param string $carpetaImagenes es la carpeta donde se guardan las imagenes
     **/
    public static function fromDocx($docx, $carpetaImagenes = null)
    {
        // Settings::setZipClass(Settings::PCLZIP);

        // Cargamos el documento Word
        $phpWord = IOFactory::load($docx);

        // Convertimos el documento a HTML
        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord);
        $htmlContent = $htmlWriter->getContent();
        Log::info("Html from docx: " . $htmlContent);

        // removemos span de texto por defecto
        //$htmlContent = preg_replace('&<span style="(?:font-family:\s*[^;]*;\s?|font-size:\s?1\dpt;\s?)+">([^<]*)</span>&', '$1', $htmlContent);
        $htmlContent = self::arreglarNotas($phpWord, $htmlContent);

        Log::info("Html final from docx after Foot notes rework: " . $htmlContent);

        $spanLimpio = true;

        // a veces devuelve un valor "", no sé porqué, por limpiar los "span"
        $htmlContent1 = self::limpiarHtml($htmlContent);
        if(!$htmlContent1 && $htmlContent) {
            $htmlContent1 = self::limpiarHtml($htmlContent, false);
            $spanLimpio = false;
        }
        $htmlContent = $htmlContent1;

        // die($htmlContent);
        Log::info("Html final from docx limpiarHtml: " . $htmlContent);


        if(trim($htmlContent))
        $htmlContent = self::extraerBody($htmlContent);


        // convertimos a formato desde HTML a markdown
        $converter = new HtmlConverter();
        $markdown = $converter->convert($htmlContent);

      

        // por si quedó algun div de página lo quitamos
        $markdown = preg_replace('/<div style="page:\s*page\d+">/', '', $markdown);

        // generar una carpeta aleatoria
        if (!$carpetaImagenes)
            $carpetaImagenes = 'temp/' . Str::random(16);

        $markdown = self::extraerImagenes($markdown, $carpetaImagenes);

        // si no se limpió en su momento, se hace ahora
        if(!$spanLimpio)
        $markdown = self::limpiarHtml($markdown);

        // arreglar enlace roto de algunos documentos
        $markdown = preg_replace('&\[tseyor.\]\(http://www.tseyor.com/\)<span style="text-decoration:\s?underline\s?">\*\*org\*\*\s?</span>&', '[tseyor.org](https://tseyor.org/)', $markdown);
        $markdown = str_replace('<span style="text-decoration:underline ">**tseyor.org**</span>', '[tseyor.org](https://tseyor.org/)', $markdown);
        $markdown = str_replace('[tseyor.](http://www.tseyor.com/)<u>org</u>', '[tseyor.org](https://tseyor.org/)', $markdown);

        return $markdown;
    }


    public static function limpiarHtml($html, $limpiarSpan = true)
    {
        
        // removemos caracteres extraños
        $html = preg_replace('/[\xA0\x0C]/u', ' ', $html);
        // Mantener saltos de línea, retorno de carro y tabulaciones al eliminar caracteres no imprimibles
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]|[\x0D]/u', '', $html);


        // removemos estilos de celda de tabla
        $html = preg_replace("/<td style=[^>]+>/", "<td>", $html);

        // eliminar atributo lang
        $html = preg_replace("&\slang=[^\s>]+&", "$1", $html);

        // se ha observado este error en documentos word:
        $html = preg_replace("&text-decoration=underline\s?&", "text-decoration:underline", $html);

        if($limpiarSpan)
        $html = preg_replace_callback(
            '&<span\s+style=([^>]+)>([^<]*)</span>&',
            function ($match) {
                // var_dump($match);
                if (!$match[2])
                    return "";

                $styles = $match[1];
                //remove first and last char
                $styles = substr($styles, 1, strlen($styles) - 2);
                $tmp = preg_split("/;\s?/", $styles, -1, PREG_SPLIT_NO_EMPTY);
                $bold = false;
                $italic = false;
                $underline = false;
                $finalStyles = [];
                foreach ($tmp as $s) {
                    $p = preg_split("/\:\s?/", $s);
                    $key = trim($p[0]);
                    $v = trim($p[1]);
                    if ($key == "font-style" && $v == "italic")
                        $italic = true;
                    else if ($key == "font-weight" && $v == "bold")
                        $bold = true;
                    else if ($key == "text-decoration" && $v == "underline")
                        $underline = true;
                    else if (!in_array($key, ['font-family', 'font-size', 'color', 'margin-bottom', 'margin-top']))
                        $finalStyles[] = "$key=$v";
                }

                $pre = "";
                $tail = "";
                if (count($finalStyles)) {
                    $pre = '<span style="' . implode('; ', $finalStyles) . '">';
                    $tail = '</span>';
                }
                if ($italic) {
                    $pre .= "<i>";
                    $tail = "</i>" . $tail;
                }
                if ($bold) {
                    $pre .= "<b>";
                    $tail = "</b>" . $tail;
                }
                if ($underline) {
                    $pre .= "<u>";
                    $tail = "</u>" . $tail;
                }

                // echo $match[2] . " - " . $styles . " - ". $match[0]. "<br>";

                return $pre . $match[2] . $tail;
            },
            $html
        );

        // dd(substr($html, 0, 2500));

        // limpiamos párrafos p
        $html = preg_replace_callback(
            '&<p\s+style=([^>]+)>(.*)</p>&',
            function ($match) {
                $styles = $match[1];

                //remove first and last char
                $styles = substr($styles, 1, strlen($styles) - 2);
                $tmp = preg_split("/;\s?/", $styles, -1, PREG_SPLIT_NO_EMPTY);
                $bold = false;
                $italic = false;
                $underline = false;
                $center = false;
                $finalStyles = [];
                foreach ($tmp as $s) {
                    $p = preg_split("/\:\s?/", $s);
                    $key = trim($p[0]);
                    $v = trim($p[1]);
                    if ($key == "font-style" && $v == "italic")
                        $italic = true;
                    else if ($key == "font-weight" && $v == "bold")
                        $bold = true;
                    else if ($key == "text-decoration" && $v == "underline")
                        $underline = true;
                    else if ($key == "text-align" && $v == "center")
                        $center = true;
                    else if (!in_array($key, ['font-family', 'font-size', 'color', 'margin-bottom', 'margin-top', 'text-align']))
                        $finalStyles[] = "$key=$v";
                }

                $pre = "<p>";
                $tail = "</p>";
                if (count($finalStyles)) {
                    $pre = '<p style="' . implode('; ', $finalStyles) . '">';
                }
                if ($center) {
                    $pre .= "{style=text-align:center}";
                }
                if ($italic) {
                    $pre .= "<i>";
                    $tail = "</i>" . $tail;
                    dd("DONT!");
                }
                if ($bold) {
                    $pre .= "<b>";
                    $tail = "</b>" . $tail;
                    dd("DONT!");
                }
                if ($underline) {
                    $pre .= "<u>";
                    $tail = "</u>" . $tail;
                    dd("DONT!");
                }
                //if($center)
                //dd($pre . $match[2] . $tail);

                // echo $match[2] . " - " . $styles . " - ". $match[0]. "<br>";

                return $pre . $match[2] . $tail;
            },
            $html
        );



        // eliminar titulos
        $html = preg_replace("&<h\d>(.*?)</h\d>&", "<p><b>$1</b></p>", $html);

        // unir estilos repetidos
        for ($i = 0; $i < 3; $i++) {
            $html = preg_replace("&<i>(.*?)</i><i>(.*?)</i>&", "<i>$1$2</i>", $html);
            $html = preg_replace("&<b>(.*?)</b><b>(.*?)</b>&", "<b>$1$2</b>", $html);
            $html = preg_replace("&<u>(.*?)</u><u>(.*?)</u>&", "<u>$1$2</u>", $html);
            $html = preg_replace(
                "&<span style=.text-decoration:underline[^>]{1,3}>(.*?)</span><span style=.text-decoration:underline[^>]{1,3}>(.*?)</span>&",
                "<span style='text-decoration:underline'>$1$2</span>",
                $html
            );
            $html = preg_replace_callback("&<a href=([^>]+)>(.*?)</a><a href=([^>]+)>(.*?)</a>&", function ($match) {
                if ($match[1] == $match[3])
                    return "<a href={$match[3]}>{$match[2]}{$match[4]}</a>";
                return $match[0];
            }, $html);
        }



        return $html;

    }


    public static function arreglarNotas($phpWord, $htmlContent)
    {
        // Arreglamos notas al pie
        // buscamos manualmente las foot notes
        $footNotes = $phpWord->getFootnotes()->getItems();
        // dd($footNotes);

        Log::info("footnotes: " . count($footNotes));
        $numNote = 0;
        // dd($footNotes);
        if ($footNotes && count($footNotes)) {
            $relationsDone = [];
            foreach ($footNotes as $note) {
                $numNote++;
                $id = $note->getRelationId();
                // para evitar repeticiones
                if (isset($relationsDone[$id]))
                    continue;
                $relationsDone[$id] = 1;

                $primerTexto = "";
                $idx = 0;
                // buscamos el primer texto relevante
                $elements = $note->getElements();
                while ($idx < count($elements) && (!$primerTexto || strlen($primerTexto) < 7)) {
                    $str = $elements[$idx++]->getText();
                    if (trim($str))
                        $primerTexto = $str;
                }

                if ($primerTexto) {
                    // buscamos el texto
                    preg_match_all("#>\s*" . str_replace(["#", "(", ")", "[", "]"], ["\\#", "\\(", "\\)", "\\[", "\\]"], $primerTexto) . "#", $htmlContent, $matches, PREG_OFFSET_CAPTURE);

                    if (count($matches[0])) {
                        // nos interesa el ultimo match (las notas están al final)
                        $pos = $matches[0][count($matches[0]) - 1][1];
                    } else
                        // si no encuentra el patrón, buscamos la posición de ese texto desde el final del documento
                        $pos = strrpos($htmlContent, $primerTexto);

                    if ($pos === FALSE) {
                        // nota no encontrada. La añadiremos manualmente al final del documento
                        $string = "";
                        for ($idx = 0; $idx < count($elements); $idx++) {
                            $string .= $elements[$idx]->getText();
                        }
                        $string = '<sup id="note-' . $numNote . '">' . $numNote . '</sup> ' . $string;
                        $htmlContent = preg_replace("#<" . "/body>#", "<p>$string</p><" . "/body>", $htmlContent);
                        Log::info("Nota $numNote no encontrada: '$primerTexto...' La añadimos manualmente al final del documento");
                    } else {
                        // buscamos desde esa posición hacia atrás, el párrafo "<p"
                        // para ello truncamos el string
                        $htmlTruncated = substr($htmlContent, 0, $pos);
                        $pos = strrpos($htmlTruncated, '<p');
                        // añadimos un id al párrafo p
                        // $htmlContent = substr($htmlContent, 0, $pos) . '<p id="note-' . $id . '" ' . substr($htmlContent, $pos + 2);
                        // buscamos el final de etiqueta
                        $pos = strpos($htmlContent, '>', $pos);
                        // insertamos el número de nota
                        $htmlContent = substr($htmlContent, 0, $pos) . '><sup id="note-' . $numNote . '">' . $id . '</sup> ' . substr($htmlContent, $pos + 1);
                        // echo $id . ' ' . $primerTexto . ' ' . $pos . '<br>';
                    }
                }
            }

            /* Reordenamos las notas al pie
              El patron de nota es <p id="note-\d
              Por ejemplo pueden estar así:

              <p>...</p>
              <p><sup id="note-3">3</sup> texto de nota al pie 3</p>
              <p><sup id="note-1">1</sup> texto de nota al pie 1</p>
              <p><sup id="note-2">2</sup> texto de nota al pie 2</p>

              Y hemos de conseguir:

              <p>...</p>
              <p><sup id="note-1">1</sup> texto de nota al pie 1</p>
              <p><sup id="note-2">2</sup> texto de nota al pie 2</p>
              <p><sup id="note-3">3</sup> texto de nota al pie 3</p>
            */

            $regex = "/<p.*? id=['\"]note-(\d+)['\"].*?<\/p>/";
            // buscamos en $htmlContent cada nota, obteniendo las posiciones
            preg_match_all($regex, $htmlContent, $matches, PREG_OFFSET_CAPTURE);
            if (count($matches[0])) {
                // para cada match extraemos el bloque de texto y lo metemos en un array
                $minOffset = strlen($htmlContent);
                $maxEnd = 0;
                $notas = [];
                foreach ($matches[0] as $idx => $m) {
                    $offset = $m[1];
                    $id = $matches[1][$idx][0];
                    $html = $m[0];
                    $notas[] = ["id" => $id, "offset" => $offset, "html" => $html];
                    $minOffset = min($minOffset, $offset);
                    $maxEnd = max($maxEnd, $offset + strlen($html));
                }

                // Ordenar el array segun id
                usort($notas, function ($a, $b) {
                    return $a['id'] - $b['id'];
                });

                // recortamos el documento
                $tail = substr($htmlContent, $maxEnd);
                $htmlContent = substr($htmlContent, 0, $minOffset);

                // añadimos las notas en orden
                foreach ($notas as $nota) {
                    $htmlContent .= $nota['html'];
                }

                $htmlContent .= $tail;

            }

        }

        self::$notasEncontradas = $numNote;

        return $htmlContent;
    }


    // Extrae las imagenes (codificadas en base64) y las guardamos en la carpeta $carpetaImagenes
    public static function extraerImagenes($markdown, $storagePathImagenes)
    {
        // Expresión regular para encontrar imágenes codificadas en base64 en el texto Markdown
        $pattern = '/!\[\]\(data:image\/([a-zA-Z]*);base64,([^)]*)\)/';

        // Obtener todas las coincidencias de imágenes codificadas en base64
        preg_match_all($pattern, $markdown, $matches);

        // array de archivos de imagen
        $imagenes = [];

        foreach ($matches[0] as $key => $match) {
            // Obtener el tipo de imagen y los datos base64
            $type = $matches[1][$key];
            $data = $matches[2][$key];

            // Decodificar los datos base64 y guardar la imagen en disco
            $imageData = base64_decode($data);
            $imageName = 'image_' . $key . '.' . $type;
            $imagePath = $storagePathImagenes . '/' . $imageName;
            $imagenes[] = Storage::disk('public')->path($imagePath);

            // Guardar la imagen en disco público
            Storage::disk('public')->put($imagePath, $imageData);

            // Obtener la URL pública de la imagen guardada
            $imageUrl = Storage::disk('public')->url($imagePath);

            // Reemplazar el enlace de la imagen codificada por la URL pública de la imagen guardada
            $markdown = str_replace($match, "![](" . $imageUrl . ")", $markdown);
        }

        self::$carpetaCreada = $storagePathImagenes;
        self::$imagenesExtraidas = $imagenes;

        return $markdown;
    }


    public static function extraerBody($html)
    {
        try {
            $dom = new \DOMDocument();
            $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

            // Extraemos el body
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
            return $bodyContent;
        } catch (\Exception $e) {
            Log::error($e);
        }


        // Encuentra el inicio del body
        $startBody = strpos($html, '<body>');
        // Encuentra el final del body
        $endBody = strpos($html, '</body>');

        // Extrae el contenido del body
        $bodyContent = substr($html, $startBody + 6, $endBody - $startBody - 6);

        $matches = [];
        // remueve div de seccion si lo hubiera
        if (preg_match_all("/<div style=.page:\s?page[^>]+>/", $bodyContent, $matches, PREG_OFFSET_CAPTURE)) {
            $divHtml = $matches[0][0][0];
            $pos = $matches[0][0][1];
            $bodyContent = substr($bodyContent, 0, $pos) . substr($bodyContent, $pos + strlen($divHtml));
            $pos = strrpos($bodyContent, "</div>");
            $bodyContent = substr($bodyContent, 0, $pos) . substr($bodyContent, $pos + strlen("</div>"));
        }

        return $bodyContent;
    }




    /**
     * Mueve las imagenes de $carpetaOrigen a $carpetaDestino
     * Modifica el texto en formato markdown
     * @param string $md Texto en formato markdown
     * @param string $carpetaOrigen Ruta de la carpeta de origen
     * @param string $carpetaDestino Ruta de la carpeta de destino
     * @return array Arreglo de imagenes movidas
     */
    public static function moverImagenes(&$md, $carpetaOrigen, $carpetaDestino, $disk = 'public'): array
    {
        $imagenes_movidas = [];

        Log::info("Markdown::moverImagenes $disk: $carpetaOrigen -> $carpetaDestino  --- $md");

        // list($disk, $carpetaOrigen) = DiskUtil::obtenerDiscoRuta($carpetaOrigen);
        // list($disk, $carpetaDestino) = DiskUtil::obtenerDiscoRuta($carpetaDestino);

         // Log::info("Markdown::moverImagenes $disk: $carpetaOrigen -> $carpetaDestino");

        // busca todas las imagenes en $md que estén en carpetaOrigen
        $expCarpetaOrigen = str_replace(["/"], ["\\/"], $carpetaOrigen);
        Log::info("/!\[(.*)\]\(($expCarpetaOrigen\/[^\)]*)\)/");
        $md = preg_replace_callback("&!\[(.*)\]\(($expCarpetaOrigen\/[^\)]*)\)&", function ($matches) use ($carpetaOrigen, $carpetaDestino, &$imagenes_movidas, $disk) {

            Log::info("match: " . print_r($matches, true));
            // extraemos el nombre de la imagen
            $imagen = $matches[2];

            // renombramos la imagen
            $nuevoNombre = $carpetaDestino . "/" . basename($imagen);

            Log::info("move1: $disk: $imagen -> $nuevoNombre");

             list($disk, $origen) = DiskUtil::obtenerDiscoRuta($imagen);
             list($disk, $destino) = DiskUtil::obtenerDiscoRuta($nuevoNombre);

             Log::info("move2: $disk: $origen -> $destino");

            // movemos la imagen a la nueva carpeta
            if(Storage::disk($disk)->copy($origen,  $destino)) {

                Storage::disk($disk)->delete($origen);

                // guardamos el movimiento de archivo
                $imagenes_movidas[] = ['desde' => $imagen, 'a' => $nuevoNombre];
                // reemplazamos el enlace con el nuevo nombre
                return "![" . $matches[1] . "](" . $nuevoNombre . ")";
            }

            // o lo dejamos como estaba
            return $matches[0];

        }, $md);

        Log::info("moverImagenes: " . print_r($imagenes_movidas, true));
        return $imagenes_movidas;
    }

    public static function removeMarkdown($content) {
        // $content = preg_replace('/\bimg\b/', '', $content); // Eliminar la palabra "img" (??)
        // eliminamos caracters de markdown
        $content = preg_replace("/[#*_]/", "", $content);
        $content = preg_replace("/!?\[([^]]*)\]\(.+\)/", "$1", $content);
        $content = preg_replace("/\{.*?\}/", "", $content); // eliminamos marcas de estilo especiales
        return $content;
    }
}
