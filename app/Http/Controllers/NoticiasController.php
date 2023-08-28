<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Noticia;
use App\Pigmalion\SEO;


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
            Noticia::select(['slug', 'titulo', 'descripcion', 'published_at'])->where('visibilidad', 'P')->latest()->paginate(10);

        $recientes = Noticia::select(['slug', 'titulo', 'published_at'])->where('visibilidad', 'P')->latest()->take(24)->get();

        return Inertia::render('Noticias/Index', [
            'filtrado' => $filtro,
            'listado' => $resultados,
            'recientes' => $recientes
        ])
            ->withViewData(SEO::get('noticias'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $noticia = Noticia::findOrFail($id);
        } else {
            $noticia = Noticia::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $noticia->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$noticia || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }

        return Inertia::render('Noticias/Noticia', [
            'noticia' => $noticia
        ])
            ->withViewData(SEO::from($noticia));
    }
}
