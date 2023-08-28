<?php

namespace App\Imports;

use \DateTime;
use \Exception;
use App\Models\Comunicado;
use App\Pigmalion\WordImport;

class ComunicadoImport
{

    public static function importar()
    {

        // Ruta del archivo "comunicados.lst"
        $lista_comunicados = base_path('resources/data/comunicados.lst');

        // Ruta de la carpeta de archivos PDF
        $rutaPdf = 'ruta/a/biblioteca/comunicados/pdf/';

        // Ruta de la carpeta de archivos MP3
        $rutaMp3 = 'ruta/a/biblioteca/comunicados/mp3/';

        // Leer el archivo línea por línea
        $lineas = file($lista_comunicados, FILE_IGNORE_NEW_LINES);

        foreach ($lineas as $linea) {
            echo "-$linea \n";
            // Separar los campos por punto y coma
            $campos = explode(';', $linea);

            // Obtener los valores de cada campo
            $numero = $campos[0];
            $categoria = $campos[1];
            $fecha = $campos[2];
            $titulo = $campos[3];
            $nombrePdf = $campos[4] . '.pdf';
            $nombresMp3 = isset($campos[5]) ? explode(',', $campos[5]) : [];

            echo "$numero $categoria $fecha $titulo ... ";

            // Comprobamos si el comunicado ya existe
            $existeComunicado = Comunicado::where('numero', $numero)
                ->where('titulo', $titulo)
                ->exists();

            if ($existeComunicado) {
                echo "ya existe\n";
            }
            else{
                echo "procesando... ";

                $archivoWord = self::searchDocx($numero, $categoria, $fecha);

                $imported = new WordImport($archivoWord);

                // Asignar los nombres de los archivos MP3
                $mp3 = [];
                foreach ($nombresMp3 as $nombreMp3) {
                    if ($nombreMp3)
                        $mp3[] = $rutaMp3 . $nombreMp3 . '.mp3';
                }

                $dateObj = DateTime::createFromFormat('d/m/y', $fecha);

                if(!$dateObj)
                {
                    echo "Error en formato de Fecha para $fecha";
                    die;
                }

                // Crear una nueva instancia de Comunicado
                $comunicado = Comunicado::create([
                    "titulo" => $titulo,
                    "texto" => $imported->content,
                    "numero" => $numero,
                    "categoria" => $categoria,
                    "fecha_comunicado"  => $dateObj->format('Y-m-d'),
                    "pdf" => $nombrePdf,
                    "audios" => json_encode($mp3)
                ]);

                // Copiaremos las imágenes a la carpeta de destino
                $imagesFolder = "media/comunicados/_{$comunicado->id}";

                // copia las imágenes desde la carpeta temporal al directorio destino
                $imported->copyImagesTo($imagesFolder);

                // reemplazar la ubicación de las imágenes en el texto del comunicado
                $comunicado->texto = preg_replace("/\bmedia\//", "$imagesFolder/", $comunicado->texto);
                $comunicado->texto = preg_replace("/\.\/media\//", "/storage/media/", $comunicado->texto);

                $comunicado->imagen = preg_replace("/\bmedia\//", "$imagesFolder/", $comunicado->imagen);
                $comunicado->imagen = preg_replace("/\.\/media\//", "/storage/media/", $comunicado->imagen);

                // Guardar el comunicado en la base de datos o realizar cualquier otra operación necesaria
                $comunicado->save();

                echo "guardado\n";
            }
        }
    }

    private static function searchDocx($numero, $categoria, $fecha)
    {
        $carpeta = "D:\documentos\TSEYOR\comunicados";

        $fechaArchivo = str_replace("/", "", $fecha);

        // Formatear el número y la categoría
        $numeroFormateado = preg_replace("/^0+/", "", $numero);
        $categoriaFormateada = strtoupper($categoria);

        // Buscar archivos que cumplan con el número y la categoría en la carpeta
        $patronBusqueda = '';

        $numeroRegex = str_replace(".", "\\.", $numeroFormateado);
        if ($categoriaFormateada === 'GEN') {
            // Patrón de búsqueda para la categoría "GEN"
            $patronBusqueda = "/^\(0*{$numeroRegex}\)/";
        } else {
            // Patrón de búsqueda para otras categorías
            $patronBusqueda = "/^\(0*{$numeroRegex}\).+{$categoriaFormateada}/";
        }

        // Obtener todos los archivos en la carpeta
        $archivosCarpeta = glob($carpeta . '/*');

        // Realizar la búsqueda con expresiones regulares y obtener los matches
        $archivos = [];
        foreach ($archivosCarpeta as $archivo) {
            $nombreArchivo = basename($archivo);
            if (preg_match($patronBusqueda, $nombreArchivo)) {
                $archivos[] = $nombreArchivo;
            }
        }

        // Verificar si se encontró algún archivo
        if (empty($archivos)) {
            throw new Exception("No se encontró ningún archivo .docx con patrón $patronBusqueda");
        }

        // Verificar que la fecha coincida en el nombre del archivo
        foreach ($archivos as $archivo) {
            $nombreArchivo = basename($archivo);

            // Utilizar expresiones regulares para extraer la fecha del nombre del archivo
            $patronFecha = '/\b(\d{6})\b/';
            preg_match($patronFecha, $nombreArchivo, $coincidencias);

            if (!empty($coincidencias) && $coincidencias[1] === $fechaArchivo) {
                return  $carpeta . "/" . $nombreArchivo;
            }
        }

        // Si no se encontró ningún archivo con la fecha especificada
        throw new Exception("No se encontró ningún archivo .docx con número=$numero, categoria=$categoria, fecha=$fecha");
    }
}
