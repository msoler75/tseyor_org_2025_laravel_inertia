<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class VideosController extends Controller
{
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        // $categoria = $request->input('categoria');

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Video::where('titulo', 'like', "%$buscar%")
                ->orWhere('descripcion', 'like', "%$buscar%");
        } else {
            // obtiene los items sin busqueda
            $resultados = Video::whereRaw("1=1");
        }

        // parámetros
       /* if ($categoria)
            $resultados = $resultados->where('categoria', 'LIKE', "%$categoria%")
            ->when($categoria === '_', function ($query) {
                $query->orderByRaw('LOWER(titulo)');
            });
*/

        $resultados = $resultados
            ->paginate(10)
        ->appends(['buscar' => $buscar/*,  'categoria' => $categoria*/]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        // $categorias = (new Normativa())->getCategorias();

        return Inertia::render('Videos', [
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
            $normativa = Normativa::findOrFail($id);
        } else {
            $normativa = Normativa::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $normativa->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$normativa || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Normativas/Normativa', [
            'normativa' => $normativa,
        ])
            ->withViewData(SEO::from($normativa));
    }
}
