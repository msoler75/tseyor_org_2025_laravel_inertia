<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Evento;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class EventosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $page = $request->input('page', 1);

        // Permiso para ver borradores
        $esEditor = optional(auth()->user())->can('administrar social');
        $query = null;

        // Fecha de referencia para la comparación (now)
        $now = Carbon::now()->toDateTimeString();

        // Orden: primeros los eventos futuros más próximos (ASC), luego futuros lejanos,
        // y al final los eventos pasados en orden inverso (más recientes primero, DESC).
        // Usamos COALESCE(fecha_inicio, published_at, '1970-01-01') para manejar fechas nulas.
        // La estrategia es: ordenar por (is_future) DESC, luego por fecha ASC para futuros,
        // y por fecha DESC para pasados.
        $orderSql = "(COALESCE(fecha_inicio, published_at, '1970-01-01') >= ?) DESC, (CASE WHEN COALESCE(fecha_inicio, published_at, '1970-01-01') >= ? THEN COALESCE(fecha_inicio, published_at, '1970-01-01') END) ASC, (CASE WHEN COALESCE(fecha_inicio, published_at, '1970-01-01') < ? THEN COALESCE(fecha_inicio, published_at, '1970-01-01') END) DESC";

        // Construir la consulta paso a paso
        $query = Evento::select("titulo", "slug", "descripcion", "imagen", "fecha_inicio", "fecha_fin", "hora_inicio", "categoria", "updated_at", "published_at", "visibilidad");

        if($buscar)
            $query->buscar($buscar);
        else
            $query->orderByRaw($orderSql, [$now, $now, $now]);

        if (!$esEditor)
            $query->publicado();

        // paginar
        $resultados = $query
            ->paginate(EventosController::$ITEMS_POR_PAGINA, ['*'], 'page', $page);

        // appends para mantener filtros en links de paginación
        $appends = [];
        if ($categoria) $appends['categoria'] = $categoria;
        if ($buscar) $appends['buscar'] = $buscar;
        if (!empty($appends)) $resultados->appends($appends);

        $categorias = Evento::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar, false);

        return Inertia::render('Eventos/Index', [
            'filtrado' => $buscar,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('eventos'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $evento = Evento::with(['centro', 'sala', 'equipo'])->findOrFail($id);
        } else {
            $evento = Evento::with(['centro', 'sala', 'equipo'])->where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $evento->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar social');
        if (!$evento || (!$publicado && !$borrador && !$editor)) {
            abort(404);
        }

        return Inertia::render('Eventos/Evento', [
            'evento' => $evento
        ])
            // https://inertiajs.com/responses
            ->withViewData(SEO::from($evento));;
    }
}
