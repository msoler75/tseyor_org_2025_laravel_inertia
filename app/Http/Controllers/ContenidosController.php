<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contenido;
use App\Models\Busqueda;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class ContenidosController extends Controller
{

    /**
     * Novedades
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        // estos tipos de datos no aparecen en Novedades
        $coleccionesExcluidas = ['paginas', 'informes', 'normativas', 'audios', 'meditaciones', 'terminos', 'lugares', 'guias'];

        $resultados = $buscar ? Contenido::search($buscar)
            //->whereNotIn('coleccion', $coleccionesExcluidas)
            ->query(function ($query) use ($coleccionesExcluidas) {
                return $query->whereNotIn('coleccion', $coleccionesExcluidas);
            })
            ->paginate(10)->appends(['buscar' => $buscar])
            :
            Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion'])
                ->where('visibilidad', 'P')
                ->whereNotIn('coleccion', $coleccionesExcluidas)
                ->latest('updated_at') // Ordenar por updated_at
                ->paginate(10);

        return Inertia::render('Novedades', [
            'filtrado' => $buscar,
            'listado' => $resultados
        ])
            ->withViewData(SEO::get('novedades'));
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
