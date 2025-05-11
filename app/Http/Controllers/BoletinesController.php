<?php

namespace App\Http\Controllers;

use App\Models\Boletin;
use Inertia\Inertia;
use App\Pigmalion\SEO;
use Illuminate\Http\Request;

class BoletinesController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los boletines recientes según la búsqueda
        $resultados = Boletin::select(['id', 'titulo', 'texto as descripcion', 'updated_at', 'tipo'])
            ->when($buscar, function ($query, $buscar) {
                $query->where('titulo', 'LIKE', "%$buscar%")
                      ->orWhere('texto', 'LIKE', "%$buscar%");
            })
            ->when($categoria, function ($query, $categoria) {
                $query->where('tipo', $categoria);
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(12)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria]);


        $categorias = (new Boletin())->getCategorias();

        return Inertia::render('Boletines/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])->withViewData(SEO::get('boletines'));
    }

    public function ver($id)
    {
        $boletin = Boletin::findOrFail($id);
        return Inertia::render('Boletines/Boletin', ['boletin' => $boletin]);
    }
}
