<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contenido;
use App\Pigmalion\SEO;
use App\Pigmalion\Busquedas;

class ContenidosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');

        $resultados = $filtro ? Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion'])
            ->where('visibilidad', 'P')
            ->whereNot('coleccion', 'paginas')
            ->where(function ($query) use ($filtro) {
                $query->where('titulo', 'like', '%' . $filtro . '%')
                    // ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                    //->orWhere('texto', 'like', '%' . $filtro . '%')
                ;
            })
            ->latest('updated_at') // Ordenar por updated_at
            ->paginate(10)->appends(['buscar' => $filtro])
            :
            Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion'])
            ->where('visibilidad', 'P')
            ->whereNot('coleccion', 'paginas')
            ->latest('updated_at') // Ordenar por updated_at
            ->paginate(10);

        return Inertia::render('Novedades', [
            'filtrado' => $filtro,
            'listado' => $resultados
        ])
            ->withViewData(SEO::get('novedades'));
    }


    public function search(Request $request)
    {
        $buscar = $request->input('q');

        $buscarFiltrado = Busquedas::descartarPalabrasComunes($buscar);

        $resultados = Contenido::search($buscarFiltrado)->paginate(10);

        if (strlen($buscar) < 3)
        Busquedas::limpiarResultados($resultados, $buscarFiltrado, true);
        else
        Busquedas::formatearResultados($resultados, $buscarFiltrado, true);

        return response()->json($resultados, 200);
    }
}
