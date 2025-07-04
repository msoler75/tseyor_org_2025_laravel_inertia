<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Publicacion;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class PublicacionesController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        $query = Publicacion::select(['slug', 'titulo', 'descripcion', 'updated_at', 'categoria'])
            ->where('visibilidad', 'P');

        if ($buscar) {
            $ids = Publicacion::search($buscar)->get()->pluck('id');
            $query->whereIn('publicaciones.id', $ids);
        }

        if (!$categoria)
            $query->latest();
        else if ($categoria == '_')
            $query->orderBy('titulo', 'asc');
        else
            $query->where('categoria', 'like', '%' . $categoria . '%');

        // devuelve los items recientes segun la busqueda
        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Publicacion())->getCategorias();

        return Inertia::render('Publicaciones/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('publicaciones'));
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $publicacion = Publicacion::findOrFail($id);
        } else {
            $publicacion = Publicacion::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $publicacion->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$publicacion || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Publicaciones/Publicacion', [
            'publicacion' => $publicacion,
        ])
            ->withViewData(SEO::from($publicacion));
    }
}
