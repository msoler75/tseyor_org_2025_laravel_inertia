<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Lugar;
use App\Pigmalion\SEO;

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

        $lugares = Lugar::select(['nombre', 'slug'])->take(50)->get();

        return Inertia::render('Lugares/Lugar', [
            'lugares' => $lugares,
            'lugar' => $lugar
        ])
            ->withViewData(SEO::from($lugar));
    }
}
