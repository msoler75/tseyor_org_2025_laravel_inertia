<?php

namespace App\Imports;

use App\Models\Libro;
use Illuminate\Support\Facades\Storage;

class LibroImport
{

    public static function importar()
    {

        function mb_ucfirst($string, $encoding = "UTF-8")
        {
            $firstChar = mb_substr($string, 0, 1, $encoding);
            $then = mb_substr($string, 1, null, $encoding);
            return mb_strtoupper($firstChar, $encoding) . $then;
        }

        // borra todos los libros
        Libro::whereRaw("true")->forceDelete();

        $carpeta_web_original = 'd:\tseyor.org';
        $carpeta_libros = $carpeta_web_original . '\biblioteca\libros\*.html';

        $palabras_a_capitalizar = [
            "junantal", "agora", "ágora", "muul", "aumnor", "adonáis", "adonais", "rasbek", "aium", "om", "melcor", "shilcars", "uommo", "atlantis", "christian",
            "noiwanak", "mo", "rhaum", "beh", "sayab", "tseek", "suut", "oksah", "ich", "kat", "tseyor", "puente", "pueblo", "orsil", "montevives", "tegoyo",
            "agguniom", "albus", "ignus", "puerto", "rico", "tantra", "yoga", "arca", "tara", "grihal", "perú", "méxico", "chile", "barcelona", "granada", "universidad", "neent"
        ];

        $palabras_a_mayusculas = ["ong", "g.a.t.o.", "i", "ii", "iii", "iv", "v", "vi", "vii", "viii", "ix", "x", "xi", "xii", "tap"];

        $htmlFiles = glob($carpeta_libros);

        echo "Preparando datos:\n";
        $dataLibros = [];

        foreach ($htmlFiles as $htmlFile) {

            $file = basename($htmlFile);
            if ($file == 'index.html') continue;
            if ($file == 'menu.html') continue;

            // Leer el contenido del archivo
            $content = file_get_contents($htmlFile);

            // Utilizar una expresión regular para extraer el comentario que contiene los datos
            preg_match('/<!--(.*?)-->/s', $content, $matches);
            if (!isset($matches[1]))
                die("Error al obtener la información");

            // Extraer los valores de los atributos del elemento HTML
            $commentContent = $matches[1];
            // decodificar $ntilde; desde html a su caracter
            $commentContent = html_entity_decode($commentContent);
            $commentData = preg_split('/;/m', $commentContent);

            $data = [];
            foreach ($commentData as $field) {
                if (!$field) continue;
                if (strpos($field, "=") === FALSE) continue;
                list($key, $value) = explode('=', $field, 2);
                $data[trim($key)] = trim(preg_replace("/;\s*$/", "", $value)); //quitamos el punto y coma final
            }

            if (!isset($data['titulo']))
                die("Título no encontrado para $file");

            $titulo = mb_strtolower($data['titulo']);

            // formateamos el título
            $palabras_titulo = preg_split("/[\s\.\(\),ºª\-]+/", $titulo, -1, PREG_SPLIT_NO_EMPTY);
            foreach ($palabras_titulo as $idx => $palabra) {
                if (in_array(mb_strtolower($palabra), $palabras_a_capitalizar)) {
                    $titulo = preg_replace("/\b$palabra\b/U", mb_ucfirst($palabra), $titulo);
                }
                if (in_array(mb_strtolower($palabra), $palabras_a_mayusculas)) {
                    // echo "mayusculas: ".$palabra."\n";
                    $titulo = preg_replace("/\b$palabra\b/U", mb_strtoupper($palabra), $titulo);
                }
            }

            $frases_titulo = preg_split("/\./", $titulo);
            // var_dump($frases_titulo);
            foreach ($frases_titulo as $idx => $frase) {
                $frase = trim($frase);
                $frases_titulo[$idx] = mb_ucfirst($frase);
            }

            // var_dump($frases_titulo);

            //echo implode(". ", $frases_titulo);;
            //die;

            $data['titulo'] = implode(". ", $frases_titulo);


            echo "- " . $data['titulo'] . "\n";

            $data['pdf_fuente'] = $carpeta_web_original . '\\' . preg_replace("/^pdf\//", "/biblioteca/libros/pdf/", urldecode($data['pdf']));
            $data['imagen_fuente'] = $carpeta_web_original . '\\' . urldecode($data['image']);


            if (!file_exists($data['pdf_fuente']))
                die("Fichero no encontrado: " . $data['pdf_fuente']);

            if (!file_exists($data['imagen_fuente']))
                die("Fichero no encontrado: " . $data['imagen_fuente']);

            $dataLibros[] = $data;
        }

        echo "-----------------------------------\n";
        echo "Procesando " . count($dataLibros) . " libros...\n";



        foreach ($dataLibros as $data) {

            echo $data['titulo'] . "\n";

            if (preg_match("/Mono/i", $data['categoria']))
                $data['categoria'] = 'Monografías';


            // Crear un nuevo modelo Libro con los datos extraídos
            $libro = new Libro([
                'titulo' => html_entity_decode($data['titulo']),
                'descripcion' => $data['descripcion'],
                'categoria' => mb_strtolower($data['categoria']),
                'visibilidad' => 'P',
                'edicion' => isset($data['edicion']) && is_numeric($data['edicion']) ? $data['edicion'] : null,
                'paginas' => isset($data['paginas']) && is_numeric($data['paginas']) ? $data['paginas'] : null
            ]);

            // Guardar el modelo en la base de datos para que se cree el ID
            $libro->save();

            $media_folder = "medios/libros/" . $libro->id;

            $imagen_destino = $media_folder . "/" . basename($data['imagen_fuente']);
            $pdf_destino = $media_folder . "/" . basename($data['pdf_fuente']);

            $imagen_destino_path = Storage::disk('public')->path($imagen_destino);
            $pdf_destino_path = Storage::disk('public')->path($pdf_destino);

            copy($data['imagen_fuente'], $imagen_destino_path);
            copy($data['pdf_fuente'], $pdf_destino_path);

            // Actualizar los campos de imagen y pdf
            $libro->update([
                'imagen' => '/almacen/' .$imagen_destino,
                'pdf' => $pdf_destino,
            ]);
        }
    }




    public static function fusionarCategoriasSimilares()
    {
        // conteo de las categorías:
        $categorias = [];
        $libros = Libro::get();

        foreach ($libros as $libro) {
            $categoriasLibro = explode(',', $libro->categoria);
            foreach ($categoriasLibro as $categoria) {
                $categoria = trim($categoria);
                if (!empty($categoria)) {
                    if (isset($categorias[$categoria])) {
                        $categorias[$categoria]++;
                    } else {
                        $categorias[$categoria] = 1;
                    }
                }
            }
        }

        // Ordenar las categorías por conteo de mayor a menor
        arsort($categorias);

        // dd($categorias);

        // Fusión de categorías similares, siendo las que tienen mayor contaje las que absorben el nombre
        $categoriasFusionadas = [];
        foreach ($categorias as $categoria => $contador) {
            $encontrada = false;
            foreach ($categoriasFusionadas as $key => $count) {
                similar_text($categoria, $key, $percent);
                if ($percent > 80) {
                    echo "similar entre $categoria y $key\n";
                    $categoriasFusionadas[$key] += $contador;
                    $encontrada = true;
                    break;
                }
            }
            if (!$encontrada) {
                $categoriasFusionadas[$categoria] = $contador;
            }
        }


        foreach ($libros as $libro) {
            // Para cada libro, miramos sus categorías y asignamos la categoría fusionada correspondiente
            $categoriasLibro = explode(',', $libro->categoria);
            $categoriasFinales = [];
            foreach ($categoriasLibro as $categoria) {
                $categoria = trim($categoria);
                if (!empty($categoria)) {
                    $categoriaFinal = $categoria;
                    foreach ($categoriasFusionadas as $fusionada => $contador) {
                        similar_text($categoria, $fusionada, $percent);
                        if ($percent > 80) {
                            $categoriaFinal = $fusionada;
                            break;
                        }
                    }
                    $categoriasFinales[] = $categoriaFinal;
                }
            }

            // Actualizar las categorías del libro
            array_unique($categoriasFinales);
            $categoriaResultado = implode(',', $categoriasFinales);
            if ($categoriaResultado != $libro->categoria) {
                $libro->categoria = $categoriaResultado;
                $libro->save();
            }
        }
    }
}
