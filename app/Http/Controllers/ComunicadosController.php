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

        $resultados = $filtro ? Comunicado::select(['slug', 'titulo', 'descripcion', 'published_at'])
            ->where('visibilidad', 'P')
            ->where(function ($query) use ($filtro) {
                $query->where('titulo', 'like', '%' . $filtro . '%')
                    ->orWhere('texto', 'like', '%' . $filtro . '%');
            })
            ->paginate(12)
            ->appends(['buscar' => $filtro])
            :
            Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->latest()->paginate(10);

        $recientes = Comunicado::select(['slug', 'titulo', 'fecha_comunicado'])->where('visibilidad', 'P')->latest()->take(24)->get();

        return Inertia::render('Comunicados/Index', [
            'filtrado' => $filtro,
            'listado' => $resultados,
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

    public function archive()
    {
        $comunicados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
        ->where('visibilidad', 'P')
        ->latest()->paginate(10);

        return Inertia::render('Comunicados/Archivo', [
            'listado' => $comunicados
        ]);
    }
}
