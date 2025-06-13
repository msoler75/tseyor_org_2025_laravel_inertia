<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Noticia;
use App\Pigmalion\SEO;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\BusquedasHelper;

class NoticiasController extends Controller
{

    public function index(Request $request)
    {
        Log::channel('mcp')->info('[NoticiasController] Parámetros recibidos', $request->all());
        $buscar = $request->input('buscar');
        Log::channel('mcp')->info('[NoticiasController] Valor de $buscar', ['buscar' => $buscar]);

        $resultados = $buscar ?
            Noticia::search($buscar)->where('visibilidad', 'P')->paginate(10)
            :
            Noticia::select(['slug', 'titulo', 'descripcion', 'published_at', 'imagen'])->where('visibilidad', 'P')->latest()->paginate(10);

        // $recientes = Noticia::select(['slug', 'titulo', 'published_at'])->where('visibilidad', 'P')->latest()->take(24)->get();

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar, false);

        return Inertia::render('Noticias/Index', [
            'filtrado' => $buscar,
            'listado' => $resultados,
            // 'recientes' => $recientes
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
