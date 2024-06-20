<?php

namespace App\Imports;

use App\Pigmalion\Markdown;

class EntradaImport
{

    public static function importar()
    {

        // borra todas las entradas
        // Entrada::all()->forceDelete();

        //Entrada::all()->delete();

        // $folder = 'd:\tseyor.org\blogs\pueblotseyor';
        $folder = 'd:\tseyor.org\blogs\ong';

        $categoria = "Pueblo Tseyor";
        $categoria = "ONG";

        $carpeta_entradas = $folder . '\*.html';

        $dates = json_decode(@file_get_contents($folder . '\.files'), true);

        $htmlFiles = glob($carpeta_entradas);

        foreach ($htmlFiles as $htmlFile) {

            $file = basename($htmlFile);
            if (in_array($file, ['index.html', 'menu.html', 'blog-admin.html', 'blog-profile.html']))
                continue;

          // if($file!="entrevista-a-orden-la-pm.html") continue;

            echo "Importando $file\n";

            // Leer el contenido del archivo
            $content = file_get_contents($htmlFile);


            if(preg_match("/{contenido}/", $content)) continue;


            // eliminar estas secuencias:
            // <div class="clear"> </div>
            $content = preg_replace("/<div class=\"clear\">(\s*|<br\s*\/?>)<\/div>/usim", "", $content);

            // data-mce-style="color: #0000ff;" style="color: rgb(0, 0, 255);"
            $content = preg_replace("/data-mce-style=\"[^\"]+?\"/ui", "", $content);

            // <div class="blog-page">
            $content = preg_replace("/<div class=\"blog-page\">/uim", "", $content);

            // <div class="editor-buttons"> </div>
            $content = preg_replace("/<div class=\"editor-buttons\">\s*?<\/div>/usim", "", $content);

            // <span class="il"> ... </span>
            $content = preg_replace("/<span class=\"il\">([^<]+)<\/span>/usim", "$1", $content);

            // <section> ... </section>
            $content = preg_replace("/<\/?section>/ui", "", $content);

            // <span style="color: rgb(0, 0, 0);"> ... </span>
            $content = preg_replace("/<span style=\"color: rgb(\d+, \d+, \d+);\">([^<]+)<\/span>/usim", "$1", $content);



            // extraer solo el body, si lo hay, o todo el content
            $content = Markdown::extraerBody($content);



            // extraer el titulo de h1 y si no lo encuentra, de h2
            preg_match('/<h1[^>]*>(.*?)<\/h1>/uis', $content, $matches);
            if (isset($matches[1])) {
                $title = $matches[1];
            } else {
                preg_match('/<h2[^>]*>(.*?)<\/h2>/uis', $content, $matches);
                if (isset($matches[1])) {
                    $title = $matches[1];
                } else {
                    $title = preg_replace("/\.html?/ui", "", $file);
                }
            }

            $title = strip_tags($title);

            $title = substr($title, 0, 250);

            $title .= "_" . random_int(0, 9) . random_int(0, 9) . random_int(0, 9);

            $date = $dates[$file]['date'] ?? null; // es la fecha en formato timestamp
            if ($date) {
                $date = date("Y-m-d H:i:s", $date);
            }

            // Crear un nuevo modelo Libro con los datos extraídos
            $entrada = new Entrada([
                'titulo' => html_entity_decode($title),
                'categoria' => $categoria,
                'texto' => "",
                'published_at' => $date,
                'visibilidad' => 'P',
            ]);

            // Guardar el modelo en la base de datos
            $entrada->save();

            $mediaFolder = "medios/entradas/" . $entrada->id;


            $content = preg_replace_callback("/(src=['\"])(.+?\.(jpe?g|png|webp|gif)(\?.*?)?['\"])/uim", function ($match) {
                if(preg_match("/http:/", $match[0]))
                    return str_replace(["(", ")"], [rawurlencode("("), rawurlencode(")")], $match[0]);

                return $match[1] . "https://tseyor.org" .
                    str_replace(["(", ")"], [rawurlencode("("), rawurlencode(")")], $match[2]);
            }, $content); // añade la ruta original de las imagenes

            $md = Markdown::toMarkdown($content, $mediaFolder);

            $md = preg_replace("/\bmedios\//U", "/almacen/medios/", $md);

            // dd($md);
            $entrada->update(["texto" => $md]);
        }
    }
}
