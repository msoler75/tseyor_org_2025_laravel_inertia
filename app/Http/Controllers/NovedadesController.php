<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Contenido;

class NovedadesController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');

        $resultados = $filtro ? Contenido::select(['slug_ref', 'titulo', 'descripcion', 'fecha', 'coleccion'])
            ->where('visibilidad', 'P')
            ->where(function ($query) use ($filtro) {
                $query->where('titulo', 'like', '%' . $filtro . '%')
                   // ->orWhere('descripcion', 'like', '%' . $filtro . '%')
                    //->orWhere('texto', 'like', '%' . $filtro . '%')
                    ;
            })
            ->paginate(10)->appends(['buscar' => $filtro])
            :
            Contenido::select(['slug_ref', 'titulo', 'descripcion', 'fecha', 'coleccion'])
            ->where('visibilidad', 'P')
            ->latest()->paginate(10);

        return Inertia::render('Novedades', [
            'filtrado' => $filtro,
            'listado' => $resultados
        ]);
    }
}
