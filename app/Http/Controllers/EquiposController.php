<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Equipo;
use App\Pigmalion\SEO;

class EquiposController extends Controller
{
    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $categoria = $request->input('categoria');

        $resultados = $categoria ?
            Equipo::withCount('usuarios')
            ->where('categoria', '=', $categoria)
            ->paginate(12)->appends(['categoria' => $categoria])
            : ($filtro ? Equipo::withCount('usuarios')
                ->where('nombre', 'like', '%' . $filtro . '%')
                ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                ->paginate(10)->appends(['buscar' => $filtro])
                :
                Equipo::withCount('usuarios')->latest()->paginate(10)
            );

        $categorias = Equipo::selectRaw('categoria as nombre, count(*) as total')
            ->groupBy('categoria')
            ->get();

        return Inertia::render('Equipos/Index', [
            'filtrado' => $filtro,
            'categoriaActiva' => $categoria,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('equipos'));
    }

    public function show($id)
    {
        $equipo = Equipo::with('usuarios')
            ->where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return Inertia::render('Equipos/Equipo', [
            'equipo' => $equipo,
        ]);
    }
}
