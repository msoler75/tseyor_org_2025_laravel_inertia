<?php

namespace App\Imports;

use App\Models\Libro;

class LibroImport
{

    public static function importar()
    {

        // borra todos los libros
        Libro::whereRaw("true")->delete();

        $carpeta_libros = 'd:\tseyor.org\biblioteca\libros\*.html';

        $media_folder = "media/libros";

        echo "Debes copiar las imagenes de los libros a $media_folder/portadas\n";
        echo "Debes copiar los archivos pdf a $media_folder/pdf\n";

        $htmlFiles = glob($carpeta_libros);

        foreach ($htmlFiles as $htmlFile) {
            // Leer el contenido del archivo
            $content = file_get_contents($htmlFile);

            // Utilizar una expresión regular para extraer el comentario que contiene los datos
            preg_match('/<!--(.*?)-->/s', $content, $matches);
            // Extraer los valores de los atributos del elemento HTML
            if (isset($matches[1])) {
                $commentContent = $matches[1];
                $commentData = preg_split('/[\r\n]+/', $commentContent);

                $data = [];
                foreach ($commentData as $field) {
                    if (!$field) continue;
                    if (strpos($field, "=") === FALSE) continue;
                    list($key, $value) = explode('=', $field, 2);
                    $data[trim($key)] = trim(preg_replace("/;\s*$/", "", $value)); //quitamos el punto y coma final
                }

                if (isset($data['titulo'])) {
                    if (preg_match("/Mono/i", $data['categoria']))
                        $data['categoria'] = 'Monografías';


                    $imagen = str_replace("/images/portadas", "$media_folder/portadas", urldecode($data['image']));
                    $pdf = str_replace("/biblioteca/libros/pdf", "$media_folder/pdf", urldecode($data['pdf']));

                    // Crear un nuevo modelo Libro con los datos extraídos
                    $libro = new Libro([
                        'titulo' => html_entity_decode($data['titulo']),
                        'descripcion' => $data['descripcion'],
                        'categoria' => mb_strtolower($data['categoria']),
                        'imagen' => $imagen,
                        'pdf' => $pdf,
                        'visibilidad' => 'P',
                        'edicion' => isset($data['edicion']) && is_numeric($data['edicion']) ? $data['edicion'] : null,
                        'paginas' => isset($data['paginas']) && is_numeric($data['paginas']) ? $data['paginas'] : null
                    ]);

                    // Guardar el modelo en la base de datos
                    $libro->save();
                } else
                    echo "$htmlFile descartado\n";
            }
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
