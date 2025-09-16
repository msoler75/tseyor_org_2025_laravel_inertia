<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Termino;
use App\Models\Contenido;
use App\Models\Libro;
use App\Pigmalion\SEO;
use App\Pigmalion\StrEx;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Pigmalion\BusquedasHelper;
use App\Pigmalion\Markdown;


class TerminosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 60;

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $letra = $request->input('letra');
        $letraLike = strtolower($letra) . "%";
        $listado = $letra ?
            Termino::select(['slug', 'nombre'])
                ->where('slug', 'LIKE', $letraLike)
                ->orderBy('nombre', 'asc')
                ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
                ->appends(['letra' => $letra])
            :
            ($buscar ? Termino::search($buscar)->paginate(self::$ITEMS_POR_PAGINA, 'page', $page)->appends(['filtrado' => $buscar])
                :
                Termino::select(['slug', 'nombre'])
                    ->orderBy('nombre', 'asc')
                    ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page));
        if ($buscar)
            BusquedasHelper::formatearResultados($listado, $buscar);
        return Inertia::render('Terminos/Index', [
            'listado' => $listado,
            'letras' => $this->listaLetras(),
            'filtrado' => $buscar,
            'letra' => $letra
        ])
            ->withViewData(SEO::get('glosario'));
    }


    public function show(Request $request, $id)
    {
        if (is_numeric($id)) {
            $termino = Termino::findOrFail($id);
        } else {
            $termino = Termino::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $termino->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$termino || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }


        if($request->has('json')) {
            return response()->json($termino);
        }


        // Obtén el término anterior
        $anterior = Termino::where('nombre', '<', $termino->nombre)
            ->orderBy('nombre', 'desc')
            ->first();

        // Obtén el término siguiente
        $siguiente = Termino::where('nombre', '>', $termino->nombre)
            ->orderBy('nombre', 'asc')
            ->first();

        // obtiene las referencias a términos y libros

        $ref_terminos = [];
        $ref_libros = [];

        if ($termino->ref_terminos) {
            $tmp = preg_split("/[,;]\s*/", $termino->ref_terminos);
            foreach ($tmp as $t) {
                $x = BusquedasHelper::buscar(Termino::class, $t)->first();
                if ($x)
                    $ref_terminos[] = ['nombre' => $t, 'slug' => $x->slug];
            }
        }

        if ($termino->ref_libros) {
            $tmp = preg_split("/[,;]/", $termino->ref_libros);
            foreach ($tmp as $t) {
                $t = preg_replace("/ed\.|edici.n/i", "", $t);
                if(strlen($t)<6) continue;
                $x = BusquedasHelper::buscar(Libro::class, $t)->first();
                if ($x) {
                    $ref_libros[] = ['titulo' => $t, 'slug' => $x->slug, 'descripcion' => $x->descripcion, 'imagen' => $x->imagen];
                }
            }
        }

        return Inertia::render('Terminos/Termino', [
            'termino' => $termino,
            'siguiente' => $siguiente,
            'anterior' => $anterior,
            'letras' => $this->listaLetras(),
            'referencias' => [
                'terminos' => $ref_terminos,
                'libros' => $ref_libros
            ]
        ])
            ->withViewData(SEO::from($termino));
    }


    private function listaLetras()
    {
        $una_semana = 60 * 24 * 7; // tiempo de cache

        return Cache::remember('letras_glosario', $una_semana, function () {
            $todos = Termino::select('nombre')->orderBy('nombre', 'asc')->get();

            $letras = [];

            foreach ($todos->toArray() as $item) {
                $letras[strtoupper(substr(Str::ascii($item['nombre']), 0, 1))] = 1;
            }

        return array_keys($letras);
    });
}

/**
 * Buscar término y devolver información completa en una sola llamada
 */
public function search(Request $request)
{
    $query = $request->input('q');
    $query = str_replace("-", " ", $query);
    $limite = $request->input('limite', 500); // Límite por defecto de 500 caracteres

    if (!$query) {
        return response()->json([
            'encontrado' => false,
            'mensaje' => 'No se proporcionó término de búsqueda'
        ]);
    }

    // Buscar el término usando Scout
    $resultados = Contenido::search($query)
        ->where('visibilidad', 'P')
        ->where('coleccion', 'terminos')
        ->get();

    if ($resultados->isEmpty()) {
        return response()->json([
            'encontrado' => false,
            'mensaje' => 'Término no encontrado'
        ]);
    }


    // Calcular y asignar score a cada resultado
    $resultados->each(function($item) use ($query) {
        $score = $item->__tntSearchScore__ ?? 0;

        $tituloNormalizado = StrEx::sanitizeAndDeaccent(strtolower($item->titulo));
        $queryNormalizado = StrEx::sanitizeAndDeaccent(strtolower($query));

        // Dar mayor peso si el título coincide exactamente
        if ($tituloNormalizado === $queryNormalizado) {
            $score += 10;
        }
        // Dar peso extra si el título empieza con el término buscado
        elseif (strpos($tituloNormalizado, $queryNormalizado) === 0) {
            $score += 8;
        }
        // Dar peso medio si el título contiene el término buscado completo
        elseif (strpos($tituloNormalizado, $queryNormalizado) !== false) {
            $score += 5;
        }
        // Dar peso menor si el término buscado contiene parte del título
        elseif (strpos($queryNormalizado, $tituloNormalizado) !== false) {
            $score += 2;
        }
        else {
            // Bonus por palabras individuales encontradas en el título
            $palabrasBusqueda = preg_split('/\s+/', trim($queryNormalizado));
            $palabrasTitulo = preg_split('/\s+/', trim($tituloNormalizado));

            foreach ($palabrasBusqueda as $palabra) {
                if (strlen($palabra) >= 3) { // Solo considerar palabras de 3+ caracteres
                    foreach ($palabrasTitulo as $palabraTitulo) {
                        // Coincidencia exacta de palabra
                        if ($palabra === $palabraTitulo) {
                            $score += 3;
                        }
                        // Palabra del título contiene la palabra buscada
                        elseif (strpos($palabraTitulo, $palabra) !== false) {
                            $score += 1;
                        }
                    }
                }
            }
        }

        // Asignar el score calculado como atributo
        $item->score = $score;
    });

    // Ordenar por score descendente
    $resultados = $resultados->sortByDesc('score');

    $termino = Termino::findOrFail($resultados->first()->id_ref);

    // extraer el titulo y mostrarlo
    if(0)
        dd($resultados->map(function($item) {
        return $item->titulo . ", TNT: ". ($item->__tntSearchScore__ ?? 0) . ", Score: " . $item->score;
    })->toArray());

    // Preparar la información completa
    $textoLimpio = Markdown::removeImages($termino->texto);
    $textoLimpio = Markdown::removeMarkdown($textoLimpio);
    $textoTruncado = $this->truncateText($textoLimpio, $limite);

    $info = [
        'encontrado' => true,
        'id' => $termino->id,
        'titulo' => $termino->nombre,
        'slug' => $termino->slug,
        'texto' => $textoTruncado,
        'descripcion' => $termino->descripcion,
        'slug_ref' => $termino->slug,
        'url_glosario' => route('termino', $termino->slug)
    ];

    return response()->json($info);
}

/**
 * Truncar texto respetando palabras completas
 */
private function truncateText($texto, $limite)
{
    if (!$texto || strlen($texto) <= $limite) {
        return $texto;
    }

    // Truncar en el límite de caracteres
    $truncado = substr($texto, 0, $limite);

    // Buscar el último espacio para no cortar palabras
    $ultimoEspacio = strrpos($truncado, ' ');

    if ($ultimoEspacio !== false && $ultimoEspacio > $limite * 0.8) {
        $truncado = substr($truncado, 0, $ultimoEspacio);
    }

    return trim($truncado) . '...';
}
}
