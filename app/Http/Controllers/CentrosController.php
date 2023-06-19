<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Centro;

class CentrosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');
        $pais = $request->input('pais');

        $resultados = $pais ?
            Centro::where('pais', '=', $pais)
            ->paginate(10)->appends(['pais' => $pais])
            : ($filtro ? Centro::where('nombre', 'like', '%' . $filtro . '%')
                ->orWhere('pais', 'like', '%' . $filtro . '%')
                ->orWhere('poblacion', 'like', '%' . $filtro . '%')
                ->paginate(10)->appends(['buscar' => $filtro])
                :
                Centro::latest()->paginate(10)
            );

        $paises = Centro::selectRaw('pais as nombre, count(*) as total')
            ->groupBy('pais')
            ->get();

        return Inertia::render('Centros/Index', [
            'filtrado' => $filtro,
            'paisActivo' => $pais,
            'listado' => $resultados,
            'paises' => $paises
        ]);
    }

    public function show($id)
    {
        $centro = Centro::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$centro) {
            abort(404); // Manejo de Centro no encontrada
        }

        return Inertia::render('Centros/Centro', [
            'centro' => $centro
        ]);
    }
}
