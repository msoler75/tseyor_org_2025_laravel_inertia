<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Lugar;
use App\Pigmalion\SEO;
use App\Models\Libro;

class LugaresController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $listado = $buscar ? Lugar::search($buscar)
            :
            Lugar::select(['nombre', 'slug', 'descripcion', 'imagen']);

        $listado = $listado->paginate(12);

        $todos = Lugar::select(['slug', 'nombre', 'categoria'])->take(1000)->get();

        return Inertia::render('Lugares/Index', [
            'listado' => $listado,
            'todos' => $todos
        ])
            ->withViewData(SEO::get('lugares'));
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $lugar = Lugar::findOrFail($id);
        } else {
            $lugar = Lugar::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $lugar->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$lugar || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }

        $libros = [];

        // obtiene el campo 'libros' de $lugar, y separa por comas, y obtiene el slug
        if($lugar->libros) {
            $slugs = preg_split("/[\s,]+/", $lugar->libros, -1, PREG_SPLIT_NO_EMPTY);
            $libros = Libro::whereIn('slug', $slugs)->get()->toArray();
        }

        $lugares = Lugar::select(['nombre', 'slug'])->take(50)->get();

        return Inertia::render('Lugares/Lugar', [
            'lugares' => $lugares,
            'lugar' => $lugar,
            'libros' => $libros
        ])
            ->withViewData(SEO::from($lugar));
    }
}
