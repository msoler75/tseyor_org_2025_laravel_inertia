<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contenido;
use App\Pigmalion\BusquedasHelper;
use App\Models\Busqueda;
use App\Pigmalion\SEO;


class ContenidosController extends Controller
{

    /**
     * Novedades
     */
    public function index(Request $request)
{
    $buscar = $request->input('buscar');

    $user = auth()->user();

    $esAdministrador = $user && $user->hasPermissionTo('administrar contenidos');

    // no aparecen en novedades
    $colecciones_excluidas = ['paginas', 'informes', 'normativas', 'audios', 'meditaciones', 'terminos', 'lugares', 'guias'];

    // atención para administradores: la búsqueda no incluye los contenidos no publicados

    $resultados = $buscar ?
    Contenido::search($buscar)
            ->query(function ($query) use ($colecciones_excluidas) {
                return $query->whereNotIn('coleccion', $colecciones_excluidas);
            })
            ->paginate(10)->appends(['buscar' => $buscar])
            :
       // los administradores ven todos los contenidos, y si están en modo publicado o borrador
       Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion', 'visibilidad'])
       ->when(!$esAdministrador, function ($query) {
           $query->where('visibilidad', 'P');
       })
       ->whereNotIn('coleccion', $colecciones_excluidas)
       ->latest('updated_at') // Ordenar por updated_at
       ->paginate(10);

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

        $collections = $request->input('collections');

        $buscarFiltrado = BusquedasHelper::descartarPalabrasComunes($buscar);

        // https://github.com/teamtnt/laravel-scout-tntsearch-driver?tab=readme-ov-file#constraints
        $contenido = new Contenido;

        // podemos restringir a un conjunto de colecciones
        if($collections) {
            $colecciones = explode(',', $collections);
            $contenido = $contenido->whereIn('coleccion', $colecciones);
        }

        // en realidad solo se va a tomar la primera página, se supone que son los resultados más puntuados
        $resultados = Contenido::search($buscarFiltrado)->constrain($contenido)->paginate(64);

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

        // almacenamos el session id
        $data['session_id'] = session()->getId();

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
