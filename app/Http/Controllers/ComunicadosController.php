<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comunicado;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;
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
        $año = $request->input('ano');
        $orden = $request->input('orden');

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Comunicado::search($buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado', 'categoria', 'ano', 'imagen'])
                ->where('visibilidad', 'P');
        }

        // parámetros
        if (is_numeric($año))
            $resultados = $resultados->where("ano", $año);

        if (is_numeric($categoria))
            $resultados = $resultados->where('categoria', $categoria);

         if (!$orden || $orden == 'recientes')
            $resultados = $resultados->orderBy('fecha_comunicado', 'DESC');
        else if ($orden == 'cronologico')
            $resultados = $resultados->orderBy('fecha_comunicado', 'ASC');

        $resultados = $resultados
            ->paginate(15)
            ->appends(['buscar' => $buscar,  'categoria' => $categoria, 'ano' => $año, 'orden' => $orden]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        return Inertia::render('Comunicados/Index', [
            'categoria' => $categoria,
            'ano' => $año,
            'orden' => $orden,
            'filtrado' => $buscar,
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
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Comunicados/Comunicado', [
            'comunicado' => $comunicado,
        ])
            ->withViewData(SEO::from($comunicado));
    }



    public function archive(Request $request)
    {
        $buscar = $request->input('buscar');

        $comunicados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->latest()->paginate(10)->appends(['buscar' => $buscar]);

        return Inertia::render('Comunicados/Archivo', [
            'listado' => $comunicados
        ]);
    }


    public function procesar() {
        // comprueba en los comunicados si hay audios que aun no se han convertido, o si hay pdf que no se han preparado con sus metadatos
        $audiosPendientes = Comunicado::where('audios', 'LIKE', '%upload%');
    }
}
