<?php

namespace App\Http\Controllers;

use App\Models\ComunicadoInteriorizacion;
use App\Pigmalion\BusquedasHelper;
use App\Pigmalion\Markdown;
use App\Pigmalion\SEO;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComunicadosInteriorizacionController extends Controller
{
    public static $ITEMS_POR_PAGINA = 15;

    /**
     * Verifica que el usuario sea admin o miembro del equipo de interiorización.
     * Aborta 403 si no tiene acceso.
     */
    private function autorizarAcceso(): void
    {
        $user = auth()->user();
        $esAdmin = $user && $user->can('administrar contenidos');
        $esIniciado = $user && $user->esIniciado();

        if (! $esAdmin && ! $esIniciado) {
            abort(403, 'No tienes acceso a los comunicados de interiorización.');
        }
    }

    public function index(Request $request)
    {
        $this->autorizarAcceso();

        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $nivel = $request->input('nivel');
        $ciclo = $request->input('ciclo');
        $ano = $request->input('ano');
        $orden = $request->input('orden');

        $user = auth()->user();
        $esAdmin = $user && $user->can('administrar contenidos');
        $esIniciado = $user && $user->esIniciado();
        $accesoCompleto = $esAdmin || $esIniciado;

        $campos = ['id', 'slug', 'titulo', 'descripcion', 'fecha_comunicado', 'nivel', 'ciclo', 'numero', 'ano', 'imagen'];
        $campos_busqueda = ['id', 'slug', 'titulo', 'descripcion', 'texto', 'fecha_comunicado', 'nivel', 'ciclo', 'numero', 'ano', 'imagen'];
        $ordenarPorIds = null;

        if ($buscar) {
            $buscar = preg_replace("/^com(unicado)?\s*(de\s*interiorizacion)?\s*/i", '', $buscar);
            [$frase_exacta, $busqueda_sin_frase_exacta] = BusquedasHelper::obtenerFraseExacta($buscar);

            if ($frase_exacta) {
                $resultados = ComunicadoInteriorizacion::select($campos_busqueda)
                    ->whereRaw('MATCH(texto) AGAINST(? IN NATURAL LANGUAGE MODE)', ["'\"".$frase_exacta."\"'"]);

                if ($busqueda_sin_frase_exacta) {
                    $resultados->whereIn('id', BusquedasHelper::buscar(ComunicadoInteriorizacion::class, $busqueda_sin_frase_exacta)->get()->pluck('id')->toArray());
                }
            } else {
                $idsPrioritarios = ComunicadoInteriorizacion::select('id')
                    ->whereRaw('MATCH(texto) AGAINST(? IN NATURAL LANGUAGE MODE)', ["'\"".$buscar."\"'"])->get()->pluck('id');

                $idsSecundarios = BusquedasHelper::buscar(ComunicadoInteriorizacion::class, $buscar)->get()->pluck('id');

                $ordenarPorIds = $idsPrioritarios->concat($idsSecundarios)->unique();

                $resultados = ComunicadoInteriorizacion::select($campos_busqueda)
                    ->whereIn('id', $ordenarPorIds);
            }
        } else {
            $resultados = ComunicadoInteriorizacion::select($campos);
        }

        // Filtrar por acceso del usuario
        if (! $accesoCompleto) {
            $resultados->where('visibilidad', 'P');
        }

        if (is_numeric($ano)) {
            $resultados->where('ano', $ano);
        }

        if (is_numeric($nivel)) {
            $resultados->where('nivel', $nivel);
        }

        if ($ciclo && $ciclo !== 'todos') {
            $resultados->where('ciclo', $ciclo);
        }

        if (! $orden || $orden == 'recientes') {
            $resultados = $resultados->orderBy('fecha_comunicado', 'DESC');
        } elseif ($orden == 'cronologico') {
            $resultados = $resultados->orderBy('fecha_comunicado', 'ASC');
        } elseif ($orden == 'relevancia' && $ordenarPorIds) {
            $resultados->orderByRaw('FIELD(id, '.$ordenarPorIds->implode(',').')');
        }

        $resultados = $resultados
            ->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends(['buscar' => $buscar, 'nivel' => $nivel, 'ciclo' => $ciclo, 'ano' => $ano, 'orden' => $orden]);

        if ($buscar) {
            BusquedasHelper::formatearResultados($resultados, $buscar, false);
        }

        // Obtener ciclos disponibles para el filtro
        $ciclos = ComunicadoInteriorizacion::select('ciclo')
            ->distinct()
            ->orderBy('ciclo', 'DESC')
            ->pluck('ciclo');

        return Inertia::render('ComunicadosInteriorizacion/Index', [
            'nivel' => $nivel,
            'ciclo' => $ciclo,
            'ano' => $ano,
            'orden' => $orden,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'ciclos' => $ciclos,
            'esIniciado' => $esIniciado,
            'busquedaValida' => BusquedasHelper::validarBusqueda($buscar),
        ])
            ->withViewData(SEO::get('comunicados-interiorizacion'));
    }

    public function show(Request $request, $slug)
    {
        $this->autorizarAcceso();

        $user = auth()->user();
        $esAdmin = $user && $user->can('administrar contenidos');
        $esIniciado = $user && $user->esIniciado();
        $accesoCompleto = $esAdmin || $esIniciado;

        $comunicado = ComunicadoInteriorizacion::where('slug', $slug)->firstOrFail();

        $borrador = request()->has('borrador');
        if (! $accesoCompleto && $comunicado->visibilidad != 'P' && ! $borrador) {
            abort(404);
        }

        if ($comunicado->fecha_comunicado) {
            $baseQuery = $accesoCompleto
                ? ComunicadoInteriorizacion::query()
                : ComunicadoInteriorizacion::where('visibilidad', 'P');

            $siguiente = (clone $baseQuery)
                ->select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'fecha_comunicado'])
                ->where('fecha_comunicado', '>', $comunicado->fecha_comunicado)
                ->orderBy('fecha_comunicado', 'asc')
                ->first();

            $anterior = (clone $baseQuery)
                ->select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'fecha_comunicado'])
                ->where('fecha_comunicado', '<', $comunicado->fecha_comunicado)
                ->orderBy('fecha_comunicado', 'desc')
                ->first();
        }

        if ($request->has('resaltar')) {
            $comunicado->texto = BusquedasHelper::resaltarPalabras($comunicado->texto, $request->input('resaltar'));
        }

        $imagenes = Markdown::images($comunicado->texto);
        $imagenesInfo = ImagenesController::info($imagenes);

        return Inertia::render('ComunicadosInteriorizacion/Comunicado', [
            'comunicado' => $comunicado,
            'siguiente' => $siguiente,
            'anterior' => $anterior,
            'imagenesInfo' => $imagenesInfo,
        ])
            ->withViewData(SEO::from($comunicado));
    }

    public function pdf($slug)
    {
        $this->autorizarAcceso();

        $user = auth()->user();
        $esAdmin = $user && $user->can('administrar contenidos');
        $esIniciado = $user && $user->esIniciado();
        $accesoCompleto = $esAdmin || $esIniciado;

        $contenido = ComunicadoInteriorizacion::where('slug', $slug)->firstOrFail();

        if (! $accesoCompleto && $contenido->visibilidad != 'P') {
            abort(404);
        }

        return $contenido->generatePdf();
    }
}
