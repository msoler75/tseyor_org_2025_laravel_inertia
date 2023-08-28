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
        $filtro = $request->input('buscar');

        $resultados = $filtro ? Entrada::where('titulo', 'like', '%' . $filtro . '%')
            ->orWhere('descripcion', 'like', '%' . $filtro . '%')
            ->orWhere('texto', 'like', '%' . $filtro . '%')
            ->paginate(12)->appends(['buscar' => $filtro])
            :
            Entrada::latest()->paginate(10);

        $recientes = Entrada::select(['slug', 'titulo', 'published_at'])->where('visibilidad', 'P')->latest()->take(24)->get();

        return Inertia::render('Entradas/Index', [
            'filtrado' => $filtro,
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
