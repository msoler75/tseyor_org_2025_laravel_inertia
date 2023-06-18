<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Entrada;

class EntradasController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');

        $resultados = $filtro ? Entrada::where('titulo', 'like', '%' . $filtro . '%')
            ->orWhere('descripcion', 'like', '%' . $filtro . '%')
            ->orWhere('texto', 'like', '%' . $filtro . '%')
            ->paginate(12)->appends(['buscar' => $filtro])
            :
            Entrada::latest()->paginate(10);

        $recientes = Entrada::select(['slug', 'titulo', 'published_at'])->where('estado', 'P')->latest()->take(24)->get();

        return Inertia::render('Entradas/Index', [
            'filtrado' => $filtro,
            'listado' => $resultados,
            'recientes' => $recientes
        ]);
    }

    public function show($id)
    {
        $entrada = Entrada::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$entrada) {
            abort(404); // Manejo de entrada no encontrada
        }

        return Inertia::render('Entradas/Entrada', [
            'entrada' => $entrada
        ]);
    }
}
