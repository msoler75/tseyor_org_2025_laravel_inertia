<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comunicado;

class ComunicadosController extends Controller
{

    public function index(Request $request)
    {
         $filtro = $request->input('buscar');

        $resultados = $filtro ? Comunicado::where('titulo', 'like', '%' . $filtro . '%')
            ->orWhere('texto', 'like', '%' . $filtro . '%')
            ->paginate(12)
            :
            Comunicado::latest()->paginate(10);

        $recientes = Comunicado::select(['slug', 'titulo', 'published_at'])->latest()->take(24)->get();

        return Inertia::render('Comunicados/Index', [
            'filtroResultados' => $filtro,
            'resultados' => $resultados,
            'recientes' => $recientes
        ]);
    }

    public function show($id)
    {
        $comunicado = Comunicado::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$comunicado) {
            abort(404); // Manejo de comunicado no encontrada
        }

        return Inertia::render('Comunicados/Comunicado', [
            'comunicado' => $comunicado
        ]);
    }

}
