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

        $resultados = $filtro ? Noticia::select(['slug', 'titulo', 'descripcion', 'published_at'])
            ->where('visibilidad', 'P')
            ->where(function ($query) use ($filtro) {
                $query->where('titulo', 'like', '%' . $filtro . '%')
                    ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                    ->orWhere('texto', 'like', '%' . $filtro . '%');
            })
            ->paginate(10)->appends(['buscar' => $filtro])
            :
            Noticia::select(['slug', 'titulo', 'descripcion', 'published_at'])->latest()->paginate(10);

        $recientes = Noticia::select(['slug', 'titulo', 'published_at'])->where('visibilidad', 'P')->latest()->take(24)->get();

        return Inertia::render('Noticias/Index', [
            'listado' => $resultados,
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
