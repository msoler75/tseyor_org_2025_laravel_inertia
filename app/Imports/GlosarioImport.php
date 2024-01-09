<?php

namespace App\Imports;

use App\Services\WordImport;
use App\Models\Termino;
use Illuminate\Support\Facades\Log;

class GlosarioImport
{


    public static function importar()
    {
        Log::info("Importando glosario. Importar()");

        $imported = new WordImport();

        Log::info("Convertido a MD. Copiando imagenes...");

        // Copiaremos las imágenes a la carpeta de destino
        $imagesFolder = "medios/glosario";

        // copia las imágenes desde la carpeta temporal al directorio destino
        $imported->copyImagesTo($imagesFolder, true);

        $glosario_md = storage_path("imports/glosario.md");

        self::borrar_temporales();

        Log::info("Guardando $glosario_md...");

        file_put_contents($glosario_md, $imported->content);

        return true;
    }


    public static function borrar_temporales()
    {
        $terminos_json = storage_path("imports/terminos.json");

        // eliminamos datos temporales
        if (file_exists($terminos_json))
            unlink($terminos_json);
    }

    public static function procesar()
    {
        Log::info("GlosarioImport::procesar()");

        $terminos_json = storage_path("imports/terminos.json");
        if (!file_exists($terminos_json)) {

            $glosario_md = storage_path("imports/glosario.md");

            $glosario = file_get_contents($glosario_md);

            Log::info("GlosarioImport::parse()");

            $terminos = self::parse($glosario);

            file_put_contents($terminos_json, json_encode($terminos, JSON_PRETTY_PRINT));
        } else {
            $json = file_get_contents($terminos_json);
            $terminos = json_decode($json, true);
        }

        $insertados = 0;
        $tiempoInicio = microtime(true);
        $tiempoLimite = 30; // Tiempo límite en segundos

        // inserta/modifica los términos
        foreach ($terminos as $idx => $nuevoTermino) {
            if (!($terminos[$idx]['insertado'] ?? false)) {
                $termino =  Termino::where('nombre', $nuevoTermino['termino'])->first();
                if (!$termino) {
                    Log::info("Creando " . $nuevoTermino['termino']);
                    $termino = Termino::create([
                        'nombre' => $nuevoTermino['termino'],
                        'texto' => $nuevoTermino['descripcion'],
                        'ref_terminos' => $nuevoTermino['ref_terminos'],
                        'ref_libros' => $nuevoTermino['ref_libros'],
                        'visibilidad' => 'P'
                    ]);
                } else {
                    // revisa si hay algun cambio en la descripción
                    if ($termino->texto != $nuevoTermino['descripcion'] || $termino->ref_terminos != $nuevoTermino['ref_terminos'] || $termino->ref_libros != $nuevoTermino['ref_libros']) {
                        Log::info("Actualizando " . $nuevoTermino['termino']);
                        $termino->texto = $nuevoTermino['descripcion'];
                        $termino->descripcion = null;
                        $termino->ref_terminos = $nuevoTermino['ref_terminos'];
                        $termino->ref_libros = $nuevoTermino['ref_libros'];
                        $termino->save();
                    }
                }
                $terminos[$idx]['insertado'] = true;
            }
            $insertados++;

            // Verifica si ha transcurrido más de 30 segundos
            $tiempoActual = microtime(true);
            $tiempoTranscurrido = $tiempoActual - $tiempoInicio;

            if ($tiempoTranscurrido >= $tiempoLimite) {
                break; // Sale del bucle si ha pasado el tiempo límite
            }
        }

        // guardamos la actualización
        file_put_contents($terminos_json, json_encode($terminos));

        return $insertados == count($terminos);
    }

    /**
     * Función de desarrollo
     */
    public static function parse($contenido)
    {
        function corregirPuntuacionGlobal($str)
        {
            $str = preg_replace("/\*\*(.+?)\*\*(?!\*)/", "<b>$1</b>", $str); // cambia primero los asteriscos dobles por negritas
            $str = preg_replace("/\*([^*><]+)\*/", "<i>$1</i>", $str); // cambia los asteriscos simples por cursiva
            $str = preg_replace("/<i>([^*><]+)<\/i>/", '_$1_', $str); // devuelve al formato markdown las cursivas
            $str = preg_replace("/<b>([^*><]+)<\/b>/", '**$1**', $str); // devuelve al formato markdown las negritas
            $str =  preg_replace("/“\*\*/", "**“", $str);  // corrige desplazamiento de negritas
            $str = preg_replace("/\*\*([“”])\*\*/", "$1", $str);
            $str = preg_replace("/^\s*> /", "", $str);
            return $str;
        }

        // Función para verificar si una palabra es una palabra en negrita
        function esPalabraNegrita($palabra)
        {
            return preg_match('/^\*\*.+\*\*$/', $palabra);
        }

        // Función para verificar si un texto contiene caracteres válidos
        function tieneCaracteresValidos($texto)
        {
            return preg_match('/[^\s\n\r]/', $texto) && strlen($texto) > 7;
        }

        // Función para limpiar la descripción
        function limpiarDescripcion($descripcion)
        {
            $descripcion = trim($descripcion);
            $descripcion = ltrim($descripcion, '.');
            $descripcion = preg_replace("/^>\s*$/mu", "", $descripcion);
            $descripcion = preg_replace("/^[\s\t]*\*[\s\t]*$/u", "", $descripcion); // quita lineas con un solo asterisco
            //$descripcion = preg_replace("/\*\*([^\*]+)\*\*/mu", "_$1_", $descripcion); // cambia las negritas por cursiva
            $descripcion = preg_replace("/\n{2,99}/mu", "\n", $descripcion);
            $descripcion = preg_replace("/\r\n/m", "\n", $descripcion);
            $descripcion = preg_replace("/\n\s*\n\s*\n/mu", "\n\n", $descripcion);
            return $descripcion;
        }

        // cambia las url de las imagenes para que apunten a la carpeta correcta
        function prepararImagenes($descripcion)
        {
            return preg_replace("#\.\/medios\/#", "/almacen/medios/glosario/", $descripcion);
        }

        function quitarCitas($descripcion)
        {
            return preg_replace("/^>(.*)/mu", "$1", $descripcion);
        }

        function unirLineas($descripcion)
        {
            return preg_replace("/[\r\n]+(\s\t)*([a-záéíóú])/u", "$1 $2", $descripcion);
        }


        // Función para buscar referencias "Véase" en la descripción
        function extraerReferencias(&$texto)
        {
            // $referencias = [];
            // preg_match_all('/\(\s*V[ée]ase\s.*\)/su', $descripcion, $matches);

            preg_match("/\(\s*V[eé]ase\s/u", $texto, $m, PREG_OFFSET_CAPTURE);
            if (!count($m)) return [];
            $inicio = $m[0][1];
            $r = [];
            if ($inicio !== false) {
                $inicioReferencias = $inicio + strlen($m[0][0]); // Determina la posición de inicio del texto después de "(Véase"
                $contador = 1;
                $fin = $inicioReferencias;

                while ($contador > 0 && $fin < strlen($texto)) {
                    if ($texto[$fin] === '(') {
                        $contador++;
                    } elseif ($texto[$fin] === ')') {
                        $contador--;
                    }
                    $fin++;
                }

                $resultado = substr($texto, $inicioReferencias, $fin - $inicioReferencias - 1);
                $texto = substr($texto,  0, $inicio) . substr($texto,  $fin + 1);
                $r[] = $resultado;
            }
            return $r;
        }


        // Función para limpiar las referencias de la descripción
        function limpiarReferencias($referencias)
        {
            $referencias = preg_replace('/\(\s*V[ée]ase\s([^)]*?)\)/mu', '$1', $referencias);
            $referencias = trim($referencias);

            return $referencias;
        }



        // Función para obtener los términos de una referencia a términos del glosario
        function obtenerTerminosReferencia($referencia)
        {
            $referencia = preg_replace('/\([^()]*\)/', '', $referencia);
            return preg_replace("/[\*_]/", "", $referencia);
        }

        // Función para limpiar caracteres tipográficos y espacios de los términos
        function limpiarTermino($terminos)
        {
            $terminos = preg_replace('/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]/', '', $terminos);
            $terminos = preg_replace('/\s+/', ' ', $terminos);
            $terminos = trim($terminos);

            return $terminos;
        }

        // Función para limpiar una referencia a una monografía o libro
        function limpiarReferenciaMonografia($referencia)
        {
            $referencia = preg_replace('/[\*]/', '', $referencia);
            $referencia = preg_replace('/[_\*,\.\s]*Monograf[ií]a[_\**,\s:]*/iu', '', $referencia);
            $referencia = preg_replace('/[_\*,\.\s]*Biblioteca[_\*,\.\s]+Tseyor[\*,\.\s]*/iu', '', $referencia);
            $referencia = preg_replace('/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s,\.:]/', '', $referencia);
            $referencia = ltrim($referencia, '.');
            $referencia = ltrim($referencia, ',');
            $referencia = rtrim($referencia, '.');
            $referencia = rtrim($referencia, ',');
            $referencia = preg_replace('/\s+/', ' ', $referencia);
            $referencia = trim($referencia);

            return $referencia;
        }

        // agrega el texto a la descripción del último término

        function agregarAlUltimo(&$terminos, $texto)
        {
            if (!count($terminos)) return;
            $terminos[count($terminos) - 1]['descripcion'] .= "\n" . $texto;
        }

        function strposx($text, $patron)
        {
            if (preg_match($patron, $text, $m, PREG_OFFSET_CAPTURE)) {
                return $m[0][1];
            } else {
                return false;
            }
        }


        // Buscar el título "GLOSARIO DE TÉRMINOS" y obtener el contenido después de él
        $posicionInicio = strposx($contenido, "/\*\*GLOSARIO\s+DE\s+T.RMINOS\*\*/u");

        $posicionFinal = strposx($contenido, "/\*\*TABLA\s+EVALUATORIA.+MANTRA.+\*\*/u");

        if ($posicionInicio !== false) {
            // Obtener el contenido después del título
            $contenidoGlosario = substr($contenido, $posicionInicio + strlen('**GLOSARIO DE TÉRMINOS**'), $posicionFinal - ($posicionInicio + strlen('**GLOSARIO DE TÉRMINOS**')));

            $contenidoGlosario = corregirPuntuacionGlobal($contenidoGlosario);

            $contenidoGlosario = quitarCitas($contenidoGlosario);

            // Dividir el contenido en palabras en negrita
            $resultados = preg_split('/^\s*(\*\*.+?\*\*)/um', $contenidoGlosario, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);

            // Almacenar los términos y descripciones en un array
            $terminos = [];
            $totalResultados = count($resultados);
            for ($i = 0; $i < $totalResultados - 1; $i++) {
                $palabra = $resultados[$i][0];

                $palabraLimpiada = limpiarTermino($palabra);

                if (!$palabraLimpiada) continue;

                $siguiente = $resultados[$i + 1][0];

                // Verificar si es una palabra en negrita
                if (esPalabraNegrita($palabra) && strlen($palabraLimpiada) > 1) {
                    // Verificar si el siguiente valor no es una palabra en negrita y no es un texto vacío
                    if (!esPalabraNegrita($siguiente) && tieneCaracteresValidos($siguiente)) {
                        $termino = trim($palabra);

                        $descripcion = $siguiente;

                        // Buscar referencias "Véase" y almacenarlas
                        $referencias = extraerReferencias($descripcion);



                        // limpiar texto de la descripcion
                        $descripcion = limpiarDescripcion($descripcion);

                        // imagenes
                        $descripcion = prepararImagenes($descripcion);

                        // Supongamos que $referencias es el array de referencias obtenido
                        $referenciasTerminos = [];
                        $referenciasLibros = [];

                        foreach ($referencias as $referencia) {
                            $referencia = preg_replace("/[\r\n]+/", " ", $referencia);
                            $referencia = limpiarReferencias($referencia);
                            $referencia = trim($referencia);

                            // Extraer las referencias de monografías del texto original y modificar el texto
                            $textoModificado = $referencia;
                            while (preg_match('/_?Monograf[ií]a[_\*:\s]*(.*)/um', $textoModificado, $matches)) {
                                $monografia = $matches[1];
                                $monografia = limpiarReferenciaMonografia($monografia);

                                $referenciasLibros[] = $monografia;

                                // Remover la referencia de monografía del texto modificado
                                $textoModificado = str_replace($matches[0], '', $textoModificado);
                            }

                            $refTerminos = obtenerTerminosReferencia($textoModificado);

                            // Limpiar caracteres tipográficos y espacios de los términos
                            $refTerminos = preg_split("/[,;]/", $refTerminos);
                            foreach ($refTerminos as $refTermino)
                                $referenciasTerminos[] = limpiarTermino($refTermino);
                        }

                        $descripcion = unirLineas($descripcion);

                        $terminos[] = [
                            'termino' => limpiarTermino($termino),
                            'descripcion' => $descripcion,
                            'ref_libros' => implode(", ", $referenciasLibros),
                            'ref_terminos' => implode(", ", $referenciasTerminos)
                        ];

                        $i++;
                    } else
                        agregarAlUltimo($terminos, $palabra);
                } else if (strlen($palabraLimpiada) > 1)
                    agregarAlUltimo($terminos, $palabra);
            }
            // Imprimir el array de términos
            return $terminos;
        } else {
            throw new \Exception("No se encontró el título 'GLOSARIO DE TÉRMINOS' en el archivo.");
        }
        return [];
    }
}
