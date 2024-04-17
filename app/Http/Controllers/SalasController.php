<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sala;
use App\Pigmalion\SEO;

class SalasController extends Controller
{

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $resultados = $buscar ? Sala::where('nombre', 'like', '%' . $buscar . '%')
                ->orWhere('descripcion', 'like', '%' . $buscar . '%')
                ->paginate(10)->appends(['buscar' => $buscar])
                :
                Sala::latest()->paginate(10)
            ;

        return Inertia::render('Salas/Index', [
            'filtrado' => $buscar,
            'listado' => $resultados,
        ])
        ->withViewData(SEO::get('salas'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $sala = Sala::findOrFail($id);
        } else {
            $sala = Sala::where('slug', $id)->firstOrFail();
        }

        if (!$sala) {
            abort(404); // Manejo de audio no encontrada
        }

        return Inertia::render('Salas/Sala', [
            'sala' => $sala
        ])
       ->withViewData(SEO::from($sala));
    }
}
