<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contenido;
use App\Pigmalion\BusquedasHelper;
use App\Models\Busqueda;
use App\Pigmalion\SEO;
use App\Pigmalion\NovedadesHelper;


class ContenidosController extends Controller
{

    /**
     * Novedades
     */
    public function index(Request $request)
{
    $buscar = $request->input('buscar');

    $resultados = $buscar ?
    Contenido::search($buscar)
            ->query(function ($query) use ($coleccionesExcluidas) {
                return $query->whereNotIn('coleccion', $coleccionesExcluidas);
            })
            ->paginate(10)->appends(['buscar' => $buscar])
            :
        NovedadesHelper::getNovedades($request->input('page', 1));



    return Inertia::render('Novedades', [
        'filtrado' => $buscar,
        'listado' => $resultados
    ])->withViewData(SEO::get('novedades'));
}


    /**
     * Buscador global
     * */
    public function search(Request $request)
    {
        $buscar = $request->input('query');

        $buscarFiltrado = BusquedasHelper::descartarPalabrasComunes($buscar);

        $resultados = Contenido::search($buscarFiltrado)->paginate(64); // en realidad solo se va a tomar la primera página, se supone que son los resultados más puntuados

        if (strlen($buscarFiltrado) < 3)
            BusquedasHelper::limpiarResultados($resultados, $buscarFiltrado, true);
        else
            BusquedasHelper::formatearResultados($resultados, $buscarFiltrado, true);

        return response()->json($resultados, 200);
    }

    /**
     * Guarda para uso informativo las búsquedas que hacen los usuarios
     */
    public function searchStore(Request $request)
    {
        // si nos pasan el click_url es que en esa búsqueda se ha realizado un click
        $data = $request->input();

        $base = url("");
        // limpia la url
        if(isset($data['click_url']))
            $data['click_url'] = str_replace($base, "", $data['click_url']);

        if ($data['id'] ?? null) {
            $busqueda = Busqueda::findOrFail($data['id']);
            $busqueda->update($data);
            return response()->json(['id' => $busqueda->id], 200);
        } else {
            $busqueda = Busqueda::create($data);
            return response()->json(['id' => $busqueda->id], 200);
        }
    }
}
