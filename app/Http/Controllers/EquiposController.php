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
            ->paginate(10)->appends(['categoria' => $categoria])
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

    public function index_utg()
    {
        $categoria = 'utg';
        $departamentos = Equipo::where('categoria', '=', $categoria)
            ->take(6)->get();

        return Inertia::render(
            'Utg/Index', [
                'departamentos' => $departamentos
            ]
        )
            ->withViewData(SEO::get('utg'));
    }

    public function show($id)
    {
        $equipo = Equipo::with(['usuarios' => function ($query) {
            $query->select('users.id', 'users.name as nombre', 'users.slug', 'profile_photo_path as avatar')
                ->take(30);
        }])
            ->where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        $totalMiembros = $equipo->usuarios()->count();

        return Inertia::render('Equipos/Equipo', [
            'equipo' => $equipo,
            'totalMiembros' => $totalMiembros,
        ]);
    }
}
