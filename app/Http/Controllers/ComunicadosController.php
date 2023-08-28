<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comunicado;
use App\Pigmalion\SEO;
use App\Pigmalion\Busquedas;
use Illuminate\Support\Facades\Auth;

class ComunicadosController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     *   https://github.com/codename-12/crud-laravel-with-spatie-permission/blob/master/app/Http/Controllers/PegawaiController.php
     *
     * @return \Illuminate\Http\Response
     */
    /*
    function __construct()
    {
         $this->middleware('permission:pegawai-list|pegawai-create|pegawai-edit|pegawai-delete', ['only' => ['index','show']]);
         $this->middleware('permission:pegawai-create', ['only' => ['create','store']]);
         $this->middleware('permission:pegawai-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:pegawai-delete', ['only' => ['destroy']]);
    }
    */

    public function index(Request $request)
    {
        $filtro_recientes = $request->input('buscar_recientes');
        $filtro_archivo = $request->input('buscar_archivo');
        $vista = $request->input('vista');

        // hay dos filtros, uno para cada tipo de vista (recientes ó archivo)

        // $tnt = new TNTSearch();
        // devuelve los comunicados recientes segun el filtro, o los más recientes si no hay filtro
        $resultados = $filtro_recientes ? Comunicado::search($filtro_recientes)
            ->paginate(10, "page_recientes")
            ->appends(['buscar_recientes' => $filtro_recientes,  'vista' => $vista])
            /*->where('visibilidad', 'P')
            ->where(function ($query) use ($filtro_recientes) {
                $query->where('titulo', 'like', '%' . $filtro_recientes . '%')
                    ->orWhere('texto', 'like', '%' . $filtro_recientes . '%');
            })
            ->paginate(10, ["*"], "page_recientes")
            ->appends(['buscar_recientes' => $filtro_recientes,  'vista' => $vista]) */
            :
            Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->latest()
            ->paginate(10, ["*"], "page_recientes")
            ->appends(['vista' => $vista]);

        // Formatear resultados de busqueda
        if($filtro_recientes)
            Busquedas::formatearResultados($resultados, $filtro_recientes);

        $recientes = Comunicado::select(['slug', 'titulo', 'fecha_comunicado'])->where('visibilidad', 'P')->latest()->take(24)->get();

        // devuelve los comunicados archivados segun el filtro, o los más recientes si no hay filtro
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
        if (is_numeric($id)) {
            $comunicado = Comunicado::findOrFail($id);
        } else {
            $comunicado = Comunicado::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado =  $comunicado->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$comunicado || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Manejo de comunicado no encontrado o no autorizado
        }

        return Inertia::render('Comunicados/Comunicado', [
            'comunicado' => $comunicado,
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
