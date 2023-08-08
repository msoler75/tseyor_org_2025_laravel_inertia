<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comunicado;
use App\Pigmalion\SEO;

class ComunicadosController extends Controller
{

    public function index(Request $request)
    {
        $filtro_recientes = $request->input('buscar_recientes');
        $filtro_archivo = $request->input('buscar_archivo');
        $vista = $request->input('vista');

        $resultados = $filtro_recientes ? Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->where(function ($query) use ($filtro_recientes) {
                $query->where('titulo', 'like', '%' . $filtro_recientes . '%')
                    ->orWhere('texto', 'like', '%' . $filtro_recientes . '%');
            })
            ->paginate(10, ["*"], "page_recientes")
            ->appends(['buscar_recientes' => $filtro_recientes,  'vista' => $vista])
            :
            Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->latest()
            ->paginate(10, ["*"], "page_recientes")
            ->appends(['vista' => $vista]);



        $recientes = Comunicado::select(['slug', 'titulo', 'fecha_comunicado'])->where('visibilidad', 'P')->latest()->take(24)->get();

        $archivo = $filtro_archivo ?
            Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->where(function ($query) use ($filtro_archivo) {
                $query->where('titulo', 'like', '%' . $filtro_archivo . '%')
                    ->orWhere('texto', 'like', '%' . $filtro_archivo . '%');
            })
            ->paginate(12, ["*"], "page_archivo")->appends(['buscar_archivo' => $filtro_archivo, 'vista' => $vista])
            :
            Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->latest()
            ->paginate(12, ["*"], "page_archivo")->appends(['buscar_archivo' => $filtro_archivo, 'vista' => $vista]);

        return Inertia::render('Comunicados/Index', [
            'vista' => $vista,
            'filtrado_reciente' => $filtro_recientes,
            'filtrado_archivo' => $filtro_archivo,
            'listado' => $resultados,
            'recientes' => $recientes,
            'archivo' => $archivo
        ])
            ->withViewData(SEO::get('comunicados'));
    }

    public function show($id)
    {
        $comunicado = Comunicado::where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        $previewMode = request()->has('preview');

        if (!$comunicado || !$previewMode && $comunicado->visibilidad !== 'P') {
            abort(404); // Manejo de comunicado no encontrada
        }

        return Inertia::render('Comunicados/Comunicado', [
            'comunicado' => $comunicado
        ])
            ->withViewData(SEO::from($comunicado));
    }

    public function archive(Request $request)
    {
        $filtro = $request->input('buscar');

        $comunicados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->latest()->paginate(10)->appends(['buscar' => $filtro]);

        return Inertia::render('Comunicados/Archivo', [
            'listado' => $comunicados
        ]);
    }
}
