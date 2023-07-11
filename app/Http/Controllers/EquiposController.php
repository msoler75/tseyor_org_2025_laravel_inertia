<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Equipo;
use App\Models\Carpeta;
use App\Pigmalion\SEO;
use App\Policies\ArchivosPolicy;

class EquiposController extends Controller
{
    /**
     * Lista de equipos
     */
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

    /**
     * Muestra un equipo o departamento
     */
    public function show($id)
    {
        $equipo = Equipo::with(['usuarios' => function ($query) {
            $query->select('users.id', 'users.name as nombre', 'users.slug', 'profile_photo_path as avatar')
                ->take(30);
        }])
            ->where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        $carpetas = Carpeta::where('group_id', '=', $equipo->id)->get();

        $ultimosArchivos = [];

        $user = auth()->user();
        $ArchivosPolicy = new ArchivosPolicy();
        foreach ($carpetas as $carpeta) {
            if ($ArchivosPolicy->leer($user, $carpeta->ruta)) {
                $archivos = $carpeta->ultimosArchivos();
                $ultimosArchivos = array_merge($ultimosArchivos, $archivos);
            }
        }

        // ordenamos con recientes primero
        usort($ultimosArchivos, function ($a, $b) {
            return $b['fecha_modificacion'] - $a['fecha_modificacion'];
        });

        $totalMiembros = $equipo->usuarios()->count();

        return Inertia::render('Equipos/Equipo', [
            'equipo' => $equipo,
            'carpetas' => $carpetas,
            'ultimosArchivos' => $ultimosArchivos,
            'totalMiembros' => $totalMiembros,
        ]);
    }


    /**
     * Página de la UTG
     */
    public function index_utg()
    {
        $categoria = 'utg';
        $departamentos = Equipo::where('categoria', '=', $categoria)
            ->take(6)->get();

        return Inertia::render(
            'Utg/Index',
            [
                'departamentos' => $departamentos
            ]
        )
            ->withViewData(SEO::get('utg'));
    }
}
