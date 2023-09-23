<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Informe;
use App\Pigmalion\SEO;
use App\Pigmalion\Busquedas;

class InformesController extends Controller
{
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Informe::search($buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Informe::select(['titulo', 'descripcion', 'updated_at', 'categoria'])
                ->where('visibilidad', 'P');
        }

        // parámetros
        if ($categoria)
            $resultados = $resultados->where('categoria', $categoria);

        $resultados = $resultados
            ->paginate(12)
            ->appends(['buscar' => $buscar,  'categoria' => $categoria]);

        if ($buscar)
            Busquedas::formatearResultados($resultados, $buscar);

        $categorias = (new Informe())->getCategorias();

        return Inertia::render('Informes/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias'=>$categorias
        ])
            ->withViewData(SEO::get('Informes'));
    }



    public function show($id)
    {
        $Informe = Informe::findOrFail($id);

        $borrador = request()->has('borrador');
        $publicado =  $Informe->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar equipos');
        if (!$Informe || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Informes/Informe', [
            'Informe' => $Informe,
        ])
            ->withViewData(SEO::from($Informe));
    }
}
