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
        $listado = Lugar::select(['slug', 'nombre', 'imagen', 'categoria'])
            ->latest()->paginate(10);

        $todos = Lugar::select(['slug', 'nombre', 'categoria'])->take(100)->get();

        return Inertia::render('Lugares/Index', [
            'listado' => $listado,
            'todos' => $todos
        ])
            ->withViewData(SEO::get('lugares'));
    }



    public function show($id)
    {
        $lugar = Lugar::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

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
