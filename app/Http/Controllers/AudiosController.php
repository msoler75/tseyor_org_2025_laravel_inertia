<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Audio;

class AudiosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Audio::where('categoria', '=', $categoria)
            ->paginate(50)->appends(['categoria' => $categoria])
            : ($filtro ? Audio::where('titulo', 'like', '%' . $filtro . '%')
                ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                ->paginate(50)->appends(['buscar' => $filtro])
                :
                Audio::latest()->paginate(50)
            );

        $categorias = Audio::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        return Inertia::render('Audios/Index', [
            'filtrado' => $filtro,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ]);
    }

    public function show($id)
    {
        $audio = Audio::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$audio) {
            abort(404); // Manejo de audio no encontrada
        }

        return Inertia::render('Audios/Audio', [
            'audio' => $audio
        ]);
    }
}
