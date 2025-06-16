<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Sala;
use App\Pigmalion\SEO;

class SalasController extends Controller
{
    public static $ITEMS_POR_PAGINA = 10;

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');

        $query = Sala::query();

        if($buscar) {
            $ids = Sala::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('salas.id', $ids);
        }
        else
            $query->orderBy('nombre', 'asc');

        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
                ->appends($request->except('page'));

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
