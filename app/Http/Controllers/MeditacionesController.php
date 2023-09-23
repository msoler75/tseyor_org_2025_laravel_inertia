<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Meditacion;
use App\Pigmalion\SEO;
use App\Pigmalion\Busquedas;

class MeditacionesController extends Controller
{
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los comunicados recientes segun la busqueda
        if ($buscar) {
            $resultados = Meditacion::search($buscar);
        } else {
            // obtiene los comunicados sin busqueda
            $resultados = Meditacion::select(['slug', 'titulo', 'descripcion', 'updated_at', 'categoria'])
                ->where('visibilidad', 'P');
        }

        // parámetros
        if (is_numeric($categoria))
            $resultados = $resultados->where('categoria', $categoria);

        $resultados = $resultados
            ->paginate(15)
            ->appends(['buscar' => $buscar,  'categoria' => $categoria]);

        if ($buscar)
            Busquedas::formatearResultados($resultados, $buscar);

        $categorias = (new Meditacion())->getCategorias();

        return Inertia::render('Meditaciones/Index', [
            'categoria' => $categoria,
            'buscar' => $buscar,
            'listado' => $resultados,
            'categorias'=>$categorias
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
            abort(404); // Manejo de comunicado no encontrado o no autorizado
        }

        return Inertia::render('Meditaciones/Meditacion', [
            'meditacion' => $meditacion,
        ])
            ->withViewData(SEO::from($meditacion));
    }
}
