<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contenido;
use App\Pigmalion\SEO;
use App\Pigmalion\Busquedas;
use Illuminate\Support\Facades\Cache;

class ContenidosController extends Controller
{

    /**
     * Novedades
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $pagina = $request->input('page', 1); // Obtener el número de página actual

        // estos tipos de datos no aparecen en Novedades
        $coleccionesExcluidas = ['paginas', 'informes', /*'meditaciones',*/'terminos', 'lugares', 'guias'];

        $cacheKey = 'contenidos_' . $buscar . '_page_' . $pagina;

        $un_dia = 60 * 24;

        $resultados = Cache::remember($cacheKey, $un_dia, function () use ($buscar, $coleccionesExcluidas) {

            return $buscar ? Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion'])
                ->where('visibilidad', 'P')
                ->whereNotIn('coleccion', $coleccionesExcluidas)
                ->where(function ($query) use ($buscar) {
                    $query->where('titulo', 'like', '%' . $buscar . '%')
                        // ->orWhere('descripcion', 'like', '%' . $buscar . '%')
                        ->orWhere('texto_busqueda', 'like', '%' . $buscar . '%')
                    ;
                })
                ->latest('updated_at') // Ordenar por updated_at
                ->paginate(10)->appends(['buscar' => $buscar])
                :
                Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion'])
                    ->where('visibilidad', 'P')
                    ->whereNotIn('coleccion', $coleccionesExcluidas)
                    ->latest('updated_at') // Ordenar por updated_at
                    ->paginate(10);

        });

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

        $buscarFiltrado = Busquedas::descartarPalabrasComunes($buscar);

        $cacheKey = 'buscar_' . $buscarFiltrado;

        return Cache::remember($cacheKey, 3600, function () use ($buscarFiltrado) {

            $resultados = Contenido::search($buscarFiltrado)->paginate(64); // en realidad solo se va a tomar la primera página, se supone que son los resultados más puntuados

            if (strlen($buscarFiltrado) < 3)
                Busquedas::limpiarResultados($resultados, $buscarFiltrado, true);
            else
                Busquedas::formatearResultados($resultados, $buscarFiltrado, true);

            return response()->json($resultados, 200);
        });
    }
}
