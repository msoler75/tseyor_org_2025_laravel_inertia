<?php

namespace App\Http\Controllers;

use App\Models\Meditacion;
use App\Pigmalion\BusquedasHelper;
use App\Pigmalion\SEO;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MeditacionesController extends Controller
{
    public static $ITEMS_POR_PAGINA = 10;

    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

        $query = Meditacion::withFavorito()
            ->publicada();

        if ($buscar) {
            $query->buscar($buscar);
        } elseif ($categoria == '_') { // todos por orden alfabético
            $query->orderByRaw('LOWER(titulo)');
        } elseif (strcasecmp($categoria, 'favoritos') === 0) {
            $query->whereNotNull('favoritos.id');
        } elseif ($categoria) {
            $query->where('categoria', $categoria);
        } else {
            $query->latest('updated_at');
        }

        $resultados = $query
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        if ($buscar) {
            BusquedasHelper::formatearResultados($resultados, $buscar);
        }

        $categorias = (new Meditacion)->getCategorias();

        return Inertia::render('Meditaciones/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias,
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
        $publicado = $meditacion->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (! $meditacion || (! $publicado && ! $borrador && ! $editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Meditaciones/Meditacion', [
            'meditacion' => $meditacion,
        ])
            ->withViewData(SEO::from($meditacion));
    }
}
