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
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        $query = Normativa::select(['slug', 'titulo', 'descripcion', 'updated_at'])
            ->publicado();

        if ($buscar)
            $query->buscar($buscar);
        else if ($categoria == '_') // todos por orden alfabético
            $query->orderBy('titulo', 'asc');
        else if ($categoria)
            $query->where('categoria', $categoria);
        else
            $query->latest('updated_at');

        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        // obtiene el listado de categorías de los Normativas
        $categorias = (new Normativa())->getCategorias();

        return Inertia::render('Normativas/Index', [
            'filtrado' => $buscar,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias,
            'busquedaValida' => BusquedasHelper::validarBusqueda($buscar)
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
