<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Tutorial;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class TutorialesController extends Controller
{
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Tutorial::search($buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Tutorial::select(['slug', 'titulo', 'descripcion', 'updated_at', 'categoria'])
                ->where('visibilidad', 'P')
                ->when($categoria === '_', function ($query) {
                    $query->orderByRaw('LOWER(titulo)');
                });
        }

        // parámetros
        if ($categoria && $categoria!='_')
            $resultados = $resultados->where('categoria', 'LIKE', "%$categoria%");

        $resultados = $resultados
            ->paginate(12)
            ->appends(['buscar' => $buscar,  'categoria' => $categoria]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Tutorial())->getCategorias();

        return Inertia::render('Tutoriales/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias'=>$categorias
        ])
            ->withViewData(SEO::get('Tutoriales'));
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $Tutorial = Tutorial::findOrFail($id);
        } else {
            $Tutorial = Tutorial::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $Tutorial->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$Tutorial || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Tutoriales/Tutorial', [
            'Tutorial' => $Tutorial,
        ])
            ->withViewData(SEO::from($Tutorial));
    }
}
