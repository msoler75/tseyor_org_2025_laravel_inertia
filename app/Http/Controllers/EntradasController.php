<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Entrada;
use App\Pigmalion\SEO;

class EntradasController extends Controller
{

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $resultados = $buscar ? Entrada::where('titulo', 'like', '%' . $buscar . '%')
            ->orWhere('descripcion', 'like', '%' . $buscar . '%')
            ->orWhere('texto', 'like', '%' . $buscar . '%')
            ->paginate(12)->appends(['buscar' => $buscar])
            :
            Entrada::latest()->paginate(10);

        $recientes = Entrada::select(['slug', 'titulo', 'published_at'])->where('visibilidad', 'P')->latest()->take(24)->get();

        return Inertia::render('Entradas/Index', [
            'filtrado' => $buscar,
            'listado' => $resultados,
            'recientes' => $recientes
        ])
            ->withViewData(SEO::get('entradas'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $entrada = Entrada::findOrFail($id);
        } else {
            $entrada = Entrada::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $entrada->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$entrada || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }

        return Inertia::render('Entradas/Entrada', [
            'entrada' => $entrada
        ])
            ->withViewData(SEO::from($entrada));;
    }
}
