<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Termino;
use App\Pigmalion\SEO;

class TerminosController extends Controller
{
    public function index(Request $request)
    {
        $listado = Termino::select(['slug', 'nombre'])
            ->latest()->paginate(20);

        $todos = Termino::select('nombre')->get();

        $letras = [];

        foreach ($todos->toArray() as $item) {
            $letras[strtoupper(substr($item['nombre'], 0, 1))] = 1;
        }

        $letras = array_keys($letras);

        return Inertia::render('Terminos/Index', [
            'listado' => $listado,
            'letras' => $letras
        ])
            ->withViewData(SEO::get('glosario'));
    }


    public function show($id)
    {
        if (is_numeric($id)) {
            $termino = Termino::findOrFail($id);
        } else {
            $termino = Termino::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $termino->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$termino || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }

        return Inertia::render('Terminos/Termino', [
            'termino' => $termino
        ])
            ->withViewData(SEO::from($termino));
    }

    /**
     * Función de desarrollo
     */
    public function parse()
    {
        $glosario = Termino::findOrFail(3);

        // file_put_contents(storage_path("glosario.md"), $glosario->texto);

        // dd(substr($glosario->texto, 0, 8192));

        $contenido = $glosario->texto;

        // Función para verificar si una palabra es una palabra en negrita
        function esPalabraNegrita($palabra)
        {
            return preg_match('/^\*\*.+\*\*$/', $palabra);
        }

        // Función para verificar si un texto contiene caracteres válidos
        function tieneCaracteresValidos($texto)
        {
            return preg_match('/[^\s\n\r]/', $texto);
        }

        // Función para limpiar la descripción
        function limpiarDescripcion($descripcion)
        {
            $descripcion = trim($descripcion);
            $descripcion = ltrim($descripcion, '.');
            $descripcion = preg_replace("/^>\s*$/mu", "", $descripcion);

            return $descripcion;
        }

        function quitarCitas($descripcion)
        {
            return preg_replace("/^>(.*)/mu", "$1", $descripcion);
        }

        // no funciona
        function unirLineas($descripcion)
        {
            return preg_replace("/[\r\n\s]+(\t\s*)([a-z])/m", "$1 $2", $descripcion);
        }


        // Función para buscar referencias "Véase" en la descripción
        function buscarReferencias($descripcion)
        {
            $referencias = [];
            preg_match_all('/\(\s*V[ée]ase\s([^)]*?)\)/mu', $descripcion, $matches);

            if (!empty($matches[1])) {
                $referencias = $matches[1];
            }

            return $referencias;
        }


        // Función para limpiar las referencias de la descripción
        function limpiarReferencias($descripcion)
        {
            $descripcion = preg_replace('/\(\s*V[ée]ase\s([^)]*?)\)/mu', '', $descripcion);
            $descripcion = trim($descripcion);

            return $descripcion;
        }



        // Función para obtener los términos de una referencia a términos del glosario
        function obtenerTerminosReferencia($referencia)
        {
            preg_match('/\*(.*?)\*/', $referencia, $matches);
            $terminos = $matches[1];

            return $terminos;
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
            $referencia = preg_replace('/(\*Monografía\*|\*Biblioteca Tseyor\*)/i', '', $referencia);
            $referencia = preg_replace('/[^a-zA-Z0-9ñÑáéíóúÁÉÍÓÚ\s]/', '', $referencia);
            $referencia = preg_replace('/\s+/', ' ', $referencia);
            $referencia = trim($referencia);

            return $referencia;
        }


        // Buscar el título "GLOSARIO DE TÉRMINOS" y obtener el contenido después de él
        $posicionInicio = strpos($contenido, '**GLOSARIO DE TÉRMINOS**');
        if ($posicionInicio !== false) {
            // Obtener el contenido después del título
            $contenidoGlosario = substr($contenido, $posicionInicio + strlen('**GLOSARIO DE TÉRMINOS**'));

            // Dividir el contenido en palabras en negrita
            $resultados = preg_split('/(\*\*.+?\*\*)/', $contenidoGlosario, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE);

            // Almacenar los términos y descripciones en un array
            $terminos = [];
            $totalResultados = count($resultados);
            for ($i = 0; $i < $totalResultados - 1; $i++) {
                $palabra = $resultados[$i][0];
                $siguiente = $resultados[$i + 1][0];

                // Verificar si es una palabra en negrita
                if (esPalabraNegrita($palabra)) {
                    // Verificar si el siguiente valor no es una palabra en negrita y no es un texto vacío
                    if (!esPalabraNegrita($siguiente) && tieneCaracteresValidos($siguiente)) {
                        $termino = trim($palabra);

                        $descripcion = quitarCitas($siguiente);

                        // Buscar referencias "Véase" y almacenarlas
                        $referencias = buscarReferencias($descripcion);

                        // Limpiar la descripción eliminando las referencias
                        $descripcion = limpiarReferencias($descripcion);

                        // limpiar
                        $descripcion = limpiarDescripcion($descripcion);





                        // Supongamos que $referencias es el array de referencias obtenido

                        $referenciasClasificadas = [];

                        foreach ($referencias as $referencia) {
                            $referencia = trim($referencia);

                            // Verificar si es una referencia a términos del glosario
                            if (strpos($referencia, '*') !== false) {
                                $refTerminos = obtenerTerminosReferencia($referencia);

                                // Limpiar caracteres tipográficos y espacios de los términos
                                $refTerminos = preg_split("/[,;]/", $refTerminos);
                                foreach($refTerminos as $refTermino)
                                $referenciasClasificadas['terminos'][] =limpiarTermino($refTermino);
                            } else {
                                // Es una referencia a una monografía o libro
                                $referencia = limpiarReferenciaMonografia($referencia);

                                $referenciasClasificadas['monografias'][] = $referencia;
                            }
                        }

                        // Imprimir las referencias clasificadas
                        // dd($referenciasClasificadas);


                        // $descripcion =unirLineas($descripcion);

                        $terminos[] = [
                            'termino' => limpiarTermino($termino),
                            'descripcion' => $descripcion,
                            'referencias' => $referenciasClasificadas
                        ];
                    }
                }
            }
            // Imprimir el array de términos
            dd($terminos);
        } else {
            echo "No se encontró el título 'GLOSARIO DE TÉRMINOS' en el archivo.\n";
        }
    }
}
