<?php

namespace App\Imports;

use \DateTime;
use \Exception;
use App\Services\WordImport;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\DiskUtil;


class Comunicado extends \App\Models\Comunicado {
    protected $revisionEnabled = false;
}


class ComunicadoImport
{

    public static function importar()
    {

        // Ruta del archivo "comunicados.lst"
        $lista_comunicados = base_path('resources/data/comunicados.lst');

        // Ruta a la carpeta donde están los audios originales
        $carpeta_audios_originales = "D:\\tseyor.org\\biblioteca\\comunicados\\audios";

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
            // $nombrePdf = $campos[4] . '.pdf';
            $nombresMp3 = isset($campos[5]) ? explode(',', $campos[5]) : [];
            $categoriaIdx = 0;
            switch ($categoria) {
                case "GEN":
                    $categoriaIdx = 0;
                    break;
                case "TAP":
                    $categoriaIdx = 1;
                    break;
                case "DDM":
                    $categoriaIdx = 2;
                    break;
                case "MUUL":
                    $categoriaIdx = 3;
                    break;
            }

            $tituloFormato = ($categoria == 'GEN' ? '' : $categoria . " ") . $numero . ". " . $titulo;

            echo "$numero $categoria $fecha $titulo ... ";

            // Comprobamos si el comunicado ya existe
            $existeComunicado = Comunicado::where('numero', $numero)
                ->where('titulo', $tituloFormato)
                ->exists();

            if ($existeComunicado) {
                echo "ya existe\n";
            } else {
                echo "procesando...\n";

                $archivoWord = self::searchDocx($numero, $categoria, $fecha);

                if (!$archivoWord) {
                    die("Importación detenida. No encontramos archivo word para este comunicado: $numero $categoria $fecha $titulo\n");
                }


                $dateObj = DateTime::createFromFormat('y/m/d', $fecha);

                if (!$dateObj) {
                    echo "Error en formato de Fecha para $fecha\n";
                    die;
                }

                $año = $dateObj->format('Y');

                // verificamos audios
                $prefijo = "TSEYOR ";
                foreach ($nombresMp3 as $nombreMp3) {
                    if ($nombreMp3) {
                        $origMp3 = realpath($carpeta_audios_originales . "/" . $prefijo . $nombreMp3 . '.mp3');
                        if(!$origMp3)
                        die("No se ha encontrado ".$carpeta_audios_originales . "/" . $prefijo . $nombreMp3 . '.mp3');
                    }
                }

                // echo "Creamos la entrada de comunicado.\n";

                $contenido = Comunicado::create([
                    "titulo" => $tituloFormato,
                    "texto" => "",
                    "numero" => $numero,
                    "categoria" => $categoriaIdx,
                    "fecha_comunicado" => $dateObj->format('Y-m-d'),
                    "ano" => $año,
                    "visibilidad" => 'B',
                ]);


                $imported = null;

                // continue; // descomentar para hacer solo la verificación de archivos de audios

                try {

                    // echo "Archivo word encontrado: ". basename($archivoWord)."\n";
                    $imported = new WordImport($archivoWord);

                    // copia las imágenes desde la carpeta temporal al directorio destino, sobreescribiendo las anteriores en la carpeta
                    $imported->copyImagesTo($contenido->getCarpetaMedios(), true);

                    // Rutas de las carpetas
                    $rutaMp3 = "/almacen/medios/comunicados/audios/$año";//$contenido->getCarpetaMedios();
                    DiskUtil::ensureDirExists($rutaMp3);

                    // Asignar los nombres de los archivos MP3
                    $mp3 = [];
                    foreach ($nombresMp3 as $nombreMp3) {
                        if ($nombreMp3) {
                            $origMp3 = realpath($carpeta_audios_originales . "/" . $prefijo . $nombreMp3 . '.mp3');
                            $destNombreMp3 = preg_replace("# (PAB|TRI|JUN)#", "", $nombreMp3);
                            $destMp3 = $rutaMp3 . "/" . $prefijo . $destNombreMp3 . '.mp3';
                            $pathDest = DiskUtil::getRealPath($destMp3);
                            echo "$origMp3 -> $pathDest [$destMp3]\n";
                            copy($origMp3, $pathDest);
                            $mp3[] = $destMp3;
                        }
                    }

                    $texto = $imported->content;

                    // cambiamos urls
                    $texto = preg_replace("/(www\.)?tseyor\.com/", "tseyor.org", $texto);

                    // $texto = preg_replace("/#{4,99}\s*/", "", $texto);

                    // reparamos error de posición en logo inicial
                    $texto = preg_replace("#(.*\!\[\]\(/almacen/medios/logos/sello_tseyor_64[^)]+\))(\**Universidad Tseyor de Granada)#", "$1\n\n$2", $texto);

                    // Crear una nueva instancia de Comunicado
                    $contenido->update([
                        "texto" => $texto,
                        "audios" => $mp3,
                    ]);

                    if (!$contenido->imagen || $contenido->imagen == "/almacen/medios/logos/sello_tseyor_64.png") {
                        $guias = ['Shilcars', 'Rasbek', 'Melcor', 'Noiwanak', 'Aumnor', 'Aium Om', 'Orjaín', 'Mo', 'Rhaum', 'Jalied'];
                        $regex = "/\b(" . implode("|", $guias) . ")\b/i";
                        if (preg_match($regex, $contenido->texto, $matches)) {
                            // Log::info("guia encontrado:" . print_r($matches, true));
                            $guia = strtolower(str_replace(["í", " "], ["i", ""], $matches[0]));
                            $contenido->update(['imagen' => "/almacen/medios/guias/$guia.jpg"]);
                        }
                    }

                    echo "guardado con id {$contenido->id}\n";
                } catch (Exception $e) {
                    echo "!!!! Exception\n";
                    if ($imported) {
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
                return $carpeta . "/" . $nombreArchivo;
            }
        }

        // Si no se encontró ningún archivo con la fecha especificada
        throw new Exception("No se encontró ningún archivo .docx con número=$numero, categoria=$categoria, fecha=$fecha");
    }


    public static function publicarComunicados() {
        $comunicados = Comunicado::where('visibilidad','B')->take(200)->get();
        $n = 1;
        foreach($comunicados as $comunicado) {
            echo $n++ . ". " .$comunicado->titulo."\n";
            $comunicado->update(['visibilidad' => 'P']);
        }

        echo "Se han publicado " . $comunicados->count(). " comunicados";
    }
}
