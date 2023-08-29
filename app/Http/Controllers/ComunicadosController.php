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
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $vista = $request->input('vista');

        // hay dos filtros, uno para cada tipo de vista (recientes ó archivo)

        // $tnt = new TNTSearch();
        // devuelve los comunicados recientes segun el filtro, o los más recientes si no hay filtro
        if($buscar) {
            $resultados = Comunicado::search($buscar);
            Busquedas::formatearResultados($resultados, $buscar);
        }
        else {
            $resultados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P');

            if(!$categoria||$categoria=='recientes')
            $resultados = $resultados->latest();
            elseif($categoria=='general')
            $resultados = $resultados->orderBy('fecha_comunicado', 'ASC');
            else if(is_numeric($categoria))
                $resultados = $resultados->whereRaw('YEAR(fecha_comunicado) ='. $categoria)->orderBy('fecha_comunicado', 'ASC');
        }

        $resultados = $resultados
            ->paginate(15)
            ->appends(['buscar' => $buscar,  'vista' => $vista, 'categoria'=>$categoria]);

        /*$recientes = Comunicado::select(['slug', 'titulo', 'fecha_comunicado'])->where('visibilidad', 'P')->latest()->take(24)->get();

        // devuelve los comunicados archivados segun el filtro, o los más recientes si no hay filtro
        $archivo = $filtro_archivo ? Comunicado::search($filtro_archivo)
            ->paginate(10, "page_archivo")
            ->appends(['buscar_archivo' => $filtro_archivo,  'vista' => $vista])
            :
            Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->latest()
            ->paginate(12, ["*"], "page_archivo")->appends(['buscar_archivo' => $filtro_archivo, 'vista' => $vista]);

        /*if($filtro_archivo)
            $archivo->transform(function ($item) {
                //unset($item['descripcion']);
                //unset($item['texto']);
                return $item;
            });
            */

                // Limpiar el texto y eliminar elementos no deseados


        return Inertia::render('Comunicados/Index', [
            'vista' => $vista,
            'categoria' => $categoria,
            'buscar' => $buscar,
            'listado' => $resultados
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
