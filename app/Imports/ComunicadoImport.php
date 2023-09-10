<?php

namespace App\Imports;

use \DateTime;
use \Exception;
use App\Models\Comunicado;
use App\Pigmalion\WordImport;
use Illuminate\Support\Str;

class ComunicadoImport
{

    public static function importar()
    {

        // Ruta del archivo "comunicados.lst"
        $lista_comunicados = base_path('resources/data/comunicados.lst');

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

            $tituloFormato = ($categoria == 'GEN' ? '' : $categoria . " ") . $numero . ". " . $titulo;

            echo "$numero $categoria $fecha $titulo ... ";

            // Comprobamos si el comunicado ya existe
            $existeComunicado = Comunicado::where('numero', $numero)
                ->where('titulo', $tituloFormato)
                ->exists();

            if ($existeComunicado) {
                echo "ya existe\n";
            } else {
                echo "procesando... ";

                $archivoWord = self::searchDocx($numero, $categoria, $fecha);
                $imported = null;

                try {

                    $imported = new WordImport($archivoWord);

                    $dateObj = DateTime::createFromFormat('y/m/d', $fecha);

                    if (!$dateObj) {
                        echo "Error en formato de Fecha para $fecha";
                        die;
                    }

                    $texto = $imported->content;

                    $texto = preg_replace("/(www\.)?tseyor\.com/", "tseyor.org", $texto);

                    $texto = preg_replace("/#{4,99}\s*/", "", $texto);

                    $año = $dateObj->format('Y');


                    // Copiaremos las imágenes a la carpeta de destino
                    $imagesFolder = "media/comunicados/$año/" . ($categoria == 'GEN' ? '' : $categoria . "_") . $numero;

                    // copia las imágenes desde la carpeta temporal al directorio destino
                    if ($imported->copyImagesTo($imagesFolder)) {
                        // reemplazar la ubicación de las imágenes en el texto del comunicado
                        $texto = preg_replace("/\bmedia\//", "$imagesFolder/", $texto);
                        $texto = preg_replace("/\.\/media\//", "/storage/media/", $texto);
                    }

                    // Rutas de las carpetas
                    $rutaPdf = 'media/comunicados/pdf/' .  $año . '/';
                    $rutaMp3 = 'media/comunicados/mp3/' .  $año . '/';

                    // Asignar los nombres de los archivos MP3
                    $mp3 = [];
                    $prefijo = "TSEYOR ";
                    foreach ($nombresMp3 as $nombreMp3) {
                        if ($nombreMp3)
                            $mp3[] = $rutaMp3 . $prefijo . $nombreMp3 . '.mp3';
                    }

                    // Crear una nueva instancia de Comunicado
                    $comunicado = Comunicado::create([
                        "titulo" => $tituloFormato,
                        "slug" => null,
                        "fecha_comunicado"  => $dateObj->format('Y-m-d'),
                        "ano" => $año,
                        "texto" => $texto,
                        "numero" => $numero,
                        "categoria" => $categoria,
                        "pdf" => $rutaPdf . $nombrePdf,
                        "audios" => json_encode($mp3),
                        "visibilidad" => 'P'
                    ]);

                    echo "guardado con id {$comunicado->id}\n";
                } catch (Exception $e) {
                    echo "!!!! Exception\n";
                    if($imported)
                    {
                        $imported->deleteTempAtEnd = false;
                        echo "zip File: {$imported->zipFile}\n";
                    }
                    throw $e;
                }
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
            $patronBusqueda = "/^\(0*{$numeroRegex}\).+{$categoriaFormateada}/i";
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
            $patronFecha = '/(\d{6})/';
            preg_match($patronFecha, $nombreArchivo, $coincidencias);

            if (!empty($coincidencias) && $coincidencias[1] === $fechaArchivo) {
                return  $carpeta . "/" . $nombreArchivo;
            }
        }

        // Si no se encontró ningún archivo con la fecha especificada
        throw new Exception("No se encontró ningún archivo .docx con número=$numero, categoria=$categoria, fecha=$fecha");
    }
}
