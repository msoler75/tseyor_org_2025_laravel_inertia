<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Psicografia;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class PsicografiasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('json'))
            return $this->json();

        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Psicografia::search($buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Psicografia::select(['slug', 'titulo', 'descripcion', 'updated_at', 'categoria', 'imagen'])
                ->when($categoria === '_', function ($query) {
                    $query->orderByRaw('LOWER(titulo)');
                })
                ->when(!$categoria, function($query) {
                    $query->orderBy('created_at', 'DESC');
                });
        }

        // parámetros
        if ($categoria)
            $resultados = $resultados->where('categoria', 'LIKE', "%$categoria%");

        $resultados = $resultados
            ->paginate(14)
            ->appends(['buscar' => $buscar,  'categoria' => $categoria]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        $categorias = (new Psicografia())->getCategorias();

        return Inertia::render('Psicografias/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias'=>$categorias
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
            if(!$psicografia)
            $psicografia = Psicografia::where('slug', $id)->firstOrFail();
        } else {
            $psicografia = Psicografia::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  true;//
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
