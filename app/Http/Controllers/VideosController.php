<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class VideosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 10;
    //
    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');

        $query = Video::select(['slug', 'titulo', 'descripcion', 'enlace', 'updated_at'])
            ->where('visibilidad', 'P');

        if ($buscar) {
            $ids = Video::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('videos.id', $ids);
        }

        $resultados = $query
            ->orderBy('created_at', 'asc')
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends(['buscar' => $buscar/*,  'categoria' => $categoria*/]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);


        // $categorias = (new Normativa())->getCategorias();

        return Inertia::render('Videos/Index', [
            // 'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            // 'categorias'=>$categorias
        ])
            ->withViewData(SEO::get('videos'));
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $video = Video::findOrFail($id);
        } else {
            $video = Video::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $video->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$video || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Videos/Video', [
            'video' => $video,
        ])
            ->withViewData(SEO::from($video));
    }
}
