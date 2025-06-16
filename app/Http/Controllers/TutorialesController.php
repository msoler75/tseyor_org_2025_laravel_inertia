<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Tutorial;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class TutorialesController extends Controller
{
    public static $ITEMS_POR_PAGINA = 12;

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        $query = Tutorial::select(['slug', 'titulo', 'descripcion', 'updated_at', 'categoria'])
            ->where('visibilidad', 'P');

        if ($buscar) {
            $ids = Tutorial::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('tutoriales.id', $ids);
        }

        if (!$categoria)
            $query->latest();
        else if ($categoria == '_')
            $query->orderBy('titulo', 'asc');
        else
            $query->where('categoria', 'like', '%' . $categoria . '%');

        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Tutorial())->getCategorias();

        return Inertia::render('Tutoriales/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('Tutoriales'));
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $tutorial = Tutorial::findOrFail($id);
        } else {
            $tutorial = Tutorial::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $tutorial->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$tutorial || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Tutoriales/Tutorial', [
            'tutorial' => $tutorial,
        ])
            ->withViewData(SEO::from($tutorial));
    }
}
