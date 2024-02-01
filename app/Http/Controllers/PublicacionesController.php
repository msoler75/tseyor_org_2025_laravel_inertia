<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Publicacion;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class PublicacionesController extends Controller
{
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Publicacion::search($buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Publicacion::select(['titulo', 'slug',  'descripcion', 'updated_at', 'categoria'])
                ->where('visibilidad', 'P');
        }

        // parámetros
        if ($categoria)
            $resultados = $resultados->where('categoria', $categoria);

        $resultados = $resultados
            ->paginate(12)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria]);

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
