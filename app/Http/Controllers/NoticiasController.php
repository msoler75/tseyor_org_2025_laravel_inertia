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
    public static $ITEMS_POR_PAGINA = 10;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $page = $request->input('page', 1);

        $query = Noticia::select(['slug', 'titulo', 'descripcion', 'updated_at', 'imagen'])->withFavorito()
            ->publicada();

        if ($buscar)
            $query->buscar($buscar);
        else
            $query->latest('updated_at');

        $resultados = $query
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar, false);

        return Inertia::render('Noticias/Index', [
            'filtrado' => $buscar,
            'listado' => $resultados,
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
