<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contenido;
use App\Pigmalion\BusquedasHelper;
use App\Models\Busqueda;
use App\Pigmalion\SEO;
use App\Pigmalion\StrEx;


class ContenidosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 64;

    /**
     * Novedades
     */
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $user = auth()->user();

        $esAdministrador = $user && $user->hasPermissionTo('administrar contenidos');

        // no aparecen en novedades
        $colecciones_excluidas = ['paginas', 'informes', 'normativas', 'audios', 'meditaciones', 'terminos', /*'lugares',*/ 'guias', 'experiencias'];

        $page = $request->input('page', 1);
        $resultados = $buscar ?
            Contenido::search($buscar)
            ->query(function ($query) use ($colecciones_excluidas) {
                return $query->whereNotIn('coleccion', $colecciones_excluidas);
            })
            ->paginate(ContenidosController::$ITEMS_POR_PAGINA, 'page', $page)
            ->appends(['buscar' => $buscar])
            :
            Contenido::select(['slug_ref', 'titulo', 'imagen', 'descripcion', 'fecha', 'coleccion', 'visibilidad'])
            ->when(!$esAdministrador, function ($query) {
                $query->where('visibilidad', 'P');
            })
            ->whereNotIn('coleccion', $colecciones_excluidas)
            ->latest('updated_at')
            ->paginate(ContenidosController::$ITEMS_POR_PAGINA, ['*'], 'page', $page);

        return Inertia::render('Novedades', [
            'filtrado' => $buscar,
            'listado' => $resultados
        ])->withViewData(SEO::get('novedades'));
    }


    /**
     * Buscador global
     * to-do: unificar tal vez con BusquedasHelper::buscarContenidos
     * */
    public function search(Request $request)
    {
        $buscar = $request->input('query');
        $buscar = str_replace("-", " ", $buscar);

        $collections = $request->input('collections');

        $buscarSinAcentos = strtolower(StrEx::removerAcentosStrtr($buscar));

        // se puede utilizar un comando al comienzo de la búsqueda para indicar en qué colección buscar
        // ejemplo: com 33, buscaría comunicados con 33
        $comandos = ['com\.?|comunicado' => 'comunicados', 'libro' => 'libros', 'blog' => 'entradas', 'articulo' => 'noticias,entradas', 'evento' => 'eventos', 'noticia' => 'noticias', 'informe' => 'informes', 'normativa' => 'normativas', 'audio' => 'audios', 'meditacion' => 'meditaciones', 'glosario' => 'terminos', 'termino' => 'terminos'];

        foreach ($comandos as $key => $value) {
            // si $buscar empieza por 'blog' entonces solo buscamos en blogs
            if (preg_match("/^($key)s?.{2,999}/i", $buscarSinAcentos, $matches)) {
                $palabras = explode(' ', $buscar);
                array_shift($palabras);
                $buscar = implode(' ', $palabras);
                $collections = $value;
                // caso especial
                if ($value == "comunicados") {
                    if (is_numeric($buscar) && strlen($buscar) < 3) {
                        $numero = preg_replace("/^0+/", "", $buscar);
                        $buscar = str_pad($numero, 2, "0", STR_PAD_LEFT) . " " . str_pad($numero, 3, "0", STR_PAD_LEFT) . " " . $buscar;
                    }
                }
                break;
            }
        }


        list($buscarFiltrado, $comunes) = BusquedasHelper::separarPalabrasComunes($buscar);

        // https://github.com/teamtnt/laravel-scout-tntsearch-driver?tab=readme-ov-file#constraints
        $filtro = new Contenido;

        // podemos restringir a un conjunto de colecciones
        if ($collections) {
            $colecciones = preg_split('/[,\s+]/', $collections, -1, PREG_SPLIT_NO_EMPTY);
            $filtro = $filtro->whereIn('coleccion', $colecciones);
        }

        $busqueda_valida = BusquedasHelper::validarBusqueda($buscar);
        // en realidad solo se va a tomar la primera página, se supone que son los resultados más puntuados
        $resultados = ($busqueda_valida ? Contenido::search($buscarFiltrado)->constrain($filtro) :
            Contenido::where('id', '-1')
        )->paginate(64);

        //dd((Contenido::search($buscarFiltrado))->get()->toArray());

        if ($busqueda_valida && !$resultados->count()) // por algun motivo algunas busquedas no las encuentra. En esos casos, buscamos manualmente
            $resultados = Contenido::where('visibilidad', 'P')->where('titulo', 'LIKE', "%$buscarFiltrado%")
                ->orWhere('texto_busqueda', 'LIKE', "%$buscarFiltrado%")
                ->paginate(40)
                ->appends($request->except('page'));


        if (strlen($buscarFiltrado) < 3)
            BusquedasHelper::limpiarResultados($resultados, $buscar, true);
        else
            BusquedasHelper::formatearResultados($resultados, $buscar, true);

        return response()->json([
            'listado' => $resultados,
            'busquedaValida' => BusquedasHelper::validarBusqueda($buscar)
        ], 200);
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
        if (isset($data['click_url']))
            $data['click_url'] = str_replace($base, "", $data['click_url']);

        // almacenamos el session id
        // $data['session_id'] = session()->getId();

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
