<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Termino;
use App\Models\Libro;
use App\Pigmalion\SEO;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use App\Pigmalion\BusquedasHelper;


class TerminosController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $letra = $request->input('letra');

        $letraLike = strtolower($letra) . "%";

        $listado = $letra ?
            Termino::select(['slug', 'nombre'])
                ->where('slug', 'LIKE', $letraLike)
                ->orderBy('nombre', 'asc')
                ->paginate(60)
                ->appends(['letra' => $letra])
            :
            ($buscar ? Termino::search($buscar)->paginate(60)->appends(['filtrado' => $buscar])
                :
                Termino::select(['slug', 'nombre'])
                    ->orderBy('nombre', 'asc')
                    ->paginate(60));

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


    public function show($id)
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
}
