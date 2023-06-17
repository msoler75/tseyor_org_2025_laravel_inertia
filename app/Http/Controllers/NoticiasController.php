<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Noticia;

class NoticiasController extends Controller
{

    public function index(Request $request)
    {
         $filtro = $request->input('buscar');

        $resultados = $filtro ? Noticia::where('titulo', 'like', '%' . $filtro . '%')
            ->orWhere('texto', 'like', '%' . $filtro . '%')
            ->paginate(12)->appends(['buscar' => $filtro])
            :
            Noticia::latest()->paginate(10);

        $recientes = Noticia::select(['slug', 'titulo', 'published_at'])->where('estado', 'P')->latest()->take(24)->get();

        return Inertia::render('Noticias/Index', [
            'filtroResultados' => $filtro,
            'resultados' => $resultados,
            'recientes' => $recientes
        ]);
    }

    public function show($id)
    {
        $noticia = Noticia::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$noticia) {
            abort(404); // Manejo de noticia no encontrada
        }

        return Inertia::render('Noticias/Noticia', [
            'noticia' => $noticia
        ]);
    }

}
