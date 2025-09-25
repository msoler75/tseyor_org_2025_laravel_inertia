<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Normativa;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class NormativasController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

                $query = Normativa::withFavorito()
            ->publicada()
            ->when($categoria === '_', function ($query) {
                $query->orderByRaw('LOWER(titulo)');
            })
            ->when($categoria && $categoria !== '_', function ($query) use ($categoria) {
                $query->where('categoria', 'LIKE', "%$categoria%");
            });

        if ($buscar) {
            $ids = Normativa::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('normativas.id', $ids);
        }
        else
            $query->latest();

        $resultados = $query
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'],'page', $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Normativa())->getCategorias();

        return Inertia::render('Normativas/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('normativas'));
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
