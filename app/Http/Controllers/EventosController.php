<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Evento;
use App\Pigmalion\SEO;

class EventosController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $page = $request->input('page', 1);

        $resultados = $categoria ?
            Evento::where('categoria', '=', $categoria)
            ->paginate(EventosController::$ITEMS_POR_PAGINA, ['*'], 'page', $page)->appends(['categoria' => $categoria])
            : ($buscar ? Evento::where('titulo', 'like', '%' . $buscar . '%')
                ->orWhere('descripcion', 'like', '%' . $buscar . '%')
                ->paginate(EventosController::$ITEMS_POR_PAGINA, ['*'], 'page', $page)->appends(['buscar' => $buscar])
                :
                Evento::latest()->paginate(EventosController::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            );

        $categorias = Evento::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

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
