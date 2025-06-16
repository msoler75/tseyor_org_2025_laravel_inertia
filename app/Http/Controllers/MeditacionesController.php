<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Meditacion;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class MeditacionesController extends Controller
{
    public static $ITEMS_POR_PAGINA = 10;
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

        $query = Meditacion::select(['slug', 'titulo', 'descripcion', 'updated_at', 'categoria'])
            ->where('visibilidad', 'P')
            ->when($categoria === '_', function ($query) {
                $query->orderByRaw('LOWER(titulo)');
            })
            ->when($categoria && $categoria !== '_', function ($query) use ($categoria) {
                $query->where('categoria', 'LIKE', "%$categoria%");
            });


        if ($buscar) {
            $ids = Meditacion::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('meditaciones.id', $ids);
        }
        else if (!$categoria)
            $query->latest();

        $resultados = $query
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Meditacion())->getCategorias();

        return Inertia::render('Meditaciones/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('meditaciones'));
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $meditacion = Meditacion::findOrFail($id);
        } else {
            $meditacion = Meditacion::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $meditacion->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$meditacion || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Meditaciones/Meditacion', [
            'meditacion' => $meditacion,
        ])
            ->withViewData(SEO::from($meditacion));
    }
}
