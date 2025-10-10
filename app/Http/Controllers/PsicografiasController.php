<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Psicografia;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class PsicografiasController extends Controller
{
    public static $ITEMS_POR_PAGINA = 14;

    public function index(Request $request)
    {
        // esto es para el puzle
        if ($request->has('json'))
            return $this->json();

        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        $query = Psicografia::select('*');

        if ($buscar)
            $query->buscar($buscar);
        else if ($categoria == '_') // todos por orden alfabético
            $query->orderBy('titulo', 'asc');
        else if ($categoria)
            $query->where('categoria', $categoria);
        else
            $query->latest();


        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Psicografia())->getCategorias();

        return Inertia::render('Psicografias/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias
        ])
            ->withViewData(SEO::get('psicografias'));
    }



    public function json()
    {
        // obtiene los items sin busqueda
        $resultados = Psicografia::select(['slug', 'titulo', 'descripcion', 'created_at', 'categoria', 'imagen'])->get();

        return response()->json($resultados);
    }



    public function show($id)
    {
        if (is_numeric($id)) {
            $psicografia = Psicografia::find($id);
            if (!$psicografia)
                $psicografia = Psicografia::where('slug', $id)->firstOrFail();
        } else {
            $psicografia = Psicografia::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  true; //
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$psicografia || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        $siguiente = Psicografia::select(['id', 'slug', 'titulo', 'imagen', 'updated_at'])
            ->where('titulo', '>', $psicografia->titulo)->orderBy('titulo', 'asc')->first();

        $anterior = Psicografia::select(['id', 'slug', 'titulo', 'imagen', 'updated_at'])
            ->where('titulo', '<', $psicografia->titulo)->orderBy('titulo', 'desc')->first();

        return Inertia::render('Psicografias/Psicografia', [
            'psicografia' => $psicografia,
            'siguiente' => $siguiente ?? null,
            'anterior' => $anterior ?? null
        ])
            ->withViewData(SEO::from($psicografia));
    }
}
