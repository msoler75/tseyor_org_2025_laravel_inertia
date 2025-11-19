<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comunicado;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcesarAudios;
use App\Pigmalion\Markdown;
use Illuminate\Support\Facades\Log;
class ComunicadosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 15;

    /**
     * Display a listing of the resource.
     *
     *   https://github.com/codename-12/crud-laravel-with-spatie-permission/blob/master/app/Http/Controllers/PegawaiController.php
     *
     * @return \Illuminate\Http\Response
     */
    /*
    function __construct()
    {
         $this->middleware('permission:pegawai-list|pegawai-create|pegawai-edit|pegawai-delete', ['only' => ['index','show']]);
         $this->middleware('permission:pegawai-create', ['only' => ['create','store']]);
         $this->middleware('permission:pegawai-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:pegawai-delete', ['only' => ['destroy']]);
    }
    */

    public function __construct()
    {
        Log::info('[ComunicadosController] Constructor ejecutado');
    }

    public function index(Request $request)
    {
        Log::info('[ComunicadosController] Entrando en index', [
            'params' => $request->all(),
            'query' => $request->query(),
        ]);

        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $año = $request->input('ano');
        $orden = $request->input('orden');
        $completo = $request->input('completo');
        if ($completo == "0")
            $completo = false;

        $campos = ['id', 'slug', 'titulo', 'descripcion', 'fecha_comunicado', 'categoria', 'ano', 'imagen'];
        $campos_busqueda = ['id', 'slug', 'titulo', 'descripcion', 'texto', 'fecha_comunicado', 'categoria', 'ano', 'imagen'];
        $ordenarPorIds = null;

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            // eliminamos prefijo por si alguien se le ocurre poner "com 45" o "comunicado TAP 45" o la palabra "comunicado"
            $buscar = preg_replace("/^com(unicado)?\s*/i", "", $buscar);
            list($frase_exacta, $busqueda_sin_frase_exacta) = BusquedasHelper::obtenerFraseExacta($buscar);
            $numero = preg_replace("/TAP|\s+/i", "", $buscar);
            // búsqueda por nº de comunicado, también acepta poner "TAP 33" para indicar que es un comunicado TAP
            if (is_numeric($numero))
                $resultados = Comunicado::select($campos)
                    ->where(function ($query) use ($numero) {
                        $query->where('numero', str_pad($numero, 3, "0", STR_PAD_LEFT))
                            ->orWhere('numero', str_pad($numero, 2, "0", STR_PAD_LEFT))
                            ->orWhere('numero', $numero);
                    });
            // buscamos frase exacta
            else if ($frase_exacta) {
                $resultados = Comunicado::select($campos_busqueda)
                    ->whereRaw("MATCH(texto) AGAINST(? IN NATURAL LANGUAGE MODE)", ["'\"" . $frase_exacta . "\"'"]);

                // debe cumplir que la frase exacta y otras palabras aparecen en el texto
                if ($busqueda_sin_frase_exacta)
                    $resultados->whereIn('id', BusquedasHelper::buscar(Comunicado::class, $busqueda_sin_frase_exacta)->get()->pluck('id')->toArray());
            } else {
                // busqueda general

                // primero buscamos la frase de búsqueda tal cual, lo cual serán resultados prioritarios
                $idsPrioritarios = Comunicado::select('id')
                    ->whereRaw("MATCH(texto) AGAINST(? IN NATURAL LANGUAGE MODE)", ["'\"" . $buscar . "\"'"])->get()->pluck('id');

                $idsSecundarios = BusquedasHelper::buscar(Comunicado::class, $buscar)->get()->pluck('id');

                // Combinamos todos los IDs, y eliminamos duplicados
                $ordenarPorIds = $idsPrioritarios->concat($idsSecundarios)->unique();

                // Ahora hacemos la consulta final
                $resultados = Comunicado::select($campos_busqueda)
                    ->whereIn('id', $ordenarPorIds);
            }
        } else {
            // obtiene los items sin busqueda
            $resultados = Comunicado::select($campos);
        }

        // solo los comunicados publicados
        $resultados->publicado();

        //y ahora filtramos por año
        if (is_numeric($año))
            $resultados->where("ano", $año);

        if (is_numeric($categoria))
            $resultados = $resultados->where('categoria', $categoria);

        if (!$orden || $orden == 'recientes')
            $resultados = $resultados->orderBy('fecha_comunicado', 'DESC');
        else if ($orden == 'cronologico')
            $resultados = $resultados->orderBy('fecha_comunicado', 'ASC');
        else if ($orden == 'relevancia' && $ordenarPorIds)
            $resultados->orderByRaw("FIELD(id, " . $ordenarPorIds->implode(',') . ")");

        // por algun motivo algunas busquedas no las encuentra, entonces usamos un buscador tradicional
        /*
        if (false && $buscar && !$resultados->get()->count()) {
            $resultados = Comunicado::where(function ($query) use ($buscar) {
                $query->where('titulo', 'LIKE', "%$buscar%")
                    ->orWhere('texto', 'LIKE', "%$buscar%");
            });

            //y ahora filtramos por año, pero claro, ya se han omitido resultados
            if (is_numeric($año))
                $resultados->where("ano", $año);

            if (is_numeric($categoria))
                $resultados = $resultados->where('categoria', $categoria);

            if (!$orden || $orden == 'recientes')
                $resultados = $resultados->orderBy('fecha_comunicado', 'DESC');
            else if ($orden == 'cronologico')
                $resultados = $resultados->orderBy('fecha_comunicado', 'ASC');
        }
        */

        $resultados = $resultados
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria, 'ano' => $año, 'orden' => $orden, 'completo' => $completo ? 1 : 0]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar, false, $completo);

        return Inertia::render('Comunicados/Index', [
            'categoria' => $categoria,
            'ano' => $año,
            'orden' => $orden,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'completo' => $completo,
            'busquedaValida' => BusquedasHelper::validarBusqueda($buscar)
        ])
            ->withViewData(SEO::get('comunicados'));
    }


    public function show(Request $request, $id)
    {
        if (is_numeric($id)) {
            $comunicado = Comunicado::findOrFail($id);
        } else {
            $comunicado = Comunicado::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $comunicado->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$comunicado || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        if ($comunicado->fecha_comunicado) {
            $siguiente = Comunicado::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'fecha_comunicado'])
                ->publicado()
                ->where('fecha_comunicado', '>', $comunicado->fecha_comunicado)->orderBy('fecha_comunicado', 'asc')->first();

            $anterior = Comunicado::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'fecha_comunicado'])
                ->publicado()
                ->where('fecha_comunicado', '<', $comunicado->fecha_comunicado)->orderBy('fecha_comunicado', 'desc')->first();
        }

        if($request->has('resaltar'))
            $comunicado->texto = BusquedasHelper::resaltarPalabras($comunicado->texto, $request->input('resaltar'));

             // toma el texto de la entrada, obtiene las imagenes, y de cada una de ellas, obtiene las dimensiones
        $imagenes = Markdown::images($comunicado->texto);
        $imagenesInfo = ImagenesController::info($imagenes);

        return Inertia::render('Comunicados/Comunicado', [
            'comunicado' => $comunicado,
            'siguiente' => $siguiente,
            'anterior' => $anterior,
            'imagenesInfo' => $imagenesInfo
        ])
            ->withViewData(SEO::from($comunicado));
    }


    /**
     * Genera un PDF desde los datos de un comunicado
     */
    public function pdf($id)
    {
        if (is_numeric($id)) {
            $contenido = Comunicado::findOrFail($id);
        } else {
            $contenido = Comunicado::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $contenido->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$contenido || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return $contenido->generatePdf();
    }




    /*
        public function archive(Request $request)
        {
            $buscar = $request->input('buscar');

            $comunicados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
                ->publicado()
                ->latest()->paginate(10)->appends(['buscar' => $buscar]);

            return Inertia::render('Comunicados/Archivo', [
                'listado' => $comunicados
            ]);
        }*/


    public function procesar()
    {
        // comprueba en los comunicados si hay audios que aun no se han convertido, o si hay pdf que no se han preparado con sus metadatos
        $comunicados = Comunicado::where('audios', 'LIKE', '%upload%');

        foreach ($comunicados->get() as $comunicado) {
            if ($comunicado->audios) {
                // dd($comunicado);

                $año = date('Y', strtotime($comunicado->fecha_comunicado));
                $folder = "medios/comunicados/audios/$año";

                dispatch(new ProcesarAudios(Comunicado::class, $comunicado->id, $folder))->onQueue('audio_processing');
            }
        }
    }
}
