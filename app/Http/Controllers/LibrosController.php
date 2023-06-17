<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Libro;

class LibrosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Libro::where('categoria', '=', $categoria)
            ->paginate(12)->appends(['categoria' => $categoria])
            :
            (
            $filtro ? Libro::where('titulo', 'like', '%' . $filtro . '%')
            ->orWhere('descripcion', 'like', '%' . $filtro . '%')
            ->paginate(10)->appends(['buscar' => $filtro])
            :
            Libro::latest()->paginate(10)
            );

        $categorias = Libro::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        return Inertia::render('Libros/Index', [
            'filtroResultados' => $filtro,
            'resultados' => $resultados,
            'categorias' => $categorias
        ]);
    }

    public function show($id)
    {
        $libro = Libro::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$libro) {
            abort(404); // Manejo de libro no encontrada
        }

        return Inertia::render('Libros/Libro', [
            'libro' => $libro
        ]);
    }
}
