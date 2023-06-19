<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Evento;

class EventosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Evento::where('categoria', '=', $categoria)
            ->paginate(12)->appends(['categoria' => $categoria])
            : ($filtro ? Evento::where('titulo', 'like', '%' . $filtro . '%')
                ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                ->paginate(10)->appends(['buscar' => $filtro])
                :
                Evento::latest()->paginate(10)
            );

        $categorias = Evento::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        return Inertia::render('Eventos/Index', [
            'filtrado' => $filtro,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ]);
    }

    public function show($id)
    {
        $evento = Evento::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$evento) {
            abort(404); // Manejo de evento no encontrada
        }

        return Inertia::render('Eventos/Evento', [
            'evento' => $evento
        ]);
    }

}
