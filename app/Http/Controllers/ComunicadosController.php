<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Comunicado;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;
use Illuminate\Support\Facades\Auth;
use App\Jobs\ProcesarAudios;

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
        $completo = $request->input('completo');
        if ($completo == "0")
            $completo = false;

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $numero = preg_replace("/TAP|\s+/i","", $buscar);
            if(is_numeric($numero))
                $resultados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado', 'categoria', 'ano', 'imagen'])
            ->where('numero', $numero)
            ->where('visibilidad', 'P');
            else
                $resultados = BusquedasHelper::buscar(Comunicado::class, $buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado', 'categoria', 'ano', 'imagen'])
                ->where('visibilidad', 'P');
        }


        // $resultados->where("titulo", "511. Un nuevo enfoque hacia los demas");

        // parámetros
        if (is_numeric($año))
            $resultados = $resultados->where("ano", $año);

        if (is_numeric($categoria))
            $resultados = $resultados->where('categoria', $categoria);

        if (!$orden || $orden == 'recientes')
            $resultados = $resultados->orderBy('fecha_comunicado', 'DESC');
        else if ($orden == 'cronologico')
            $resultados = $resultados->orderBy('fecha_comunicado', 'ASC');

        if ($buscar && !$resultados->get()->count()) // por algun motivo algunas busquedas no las encuentra
            $resultados = Comunicado::where('titulo', 'LIKE', "%$buscar%")->orWhere('texto', 'LIKE', "%$buscar%");

        $resultados = $resultados
            ->paginate(16)
            ->appends(['buscar' => $buscar, 'categoria' => $categoria, 'ano' => $año, 'orden' => $orden, 'completo' => $completo ? 1 : 0]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar, false, $completo);

        return Inertia::render('Comunicados/Index', [
            'categoria' => $categoria,
            'ano' => $año,
            'orden' => $orden,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'completo' => $completo,
            'busquedaValida' => BusquedasHelper::validarBusqueda($buscar)
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
        $publicado = $comunicado->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$comunicado || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        if($comunicado->fecha_comunicado) {
            $siguiente = Comunicado::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->where('fecha_comunicado', '>', $comunicado->fecha_comunicado)->orderBy('fecha_comunicado', 'asc')->first();

            $anterior = Comunicado::select(['id', 'slug', 'titulo', 'imagen', 'descripcion', 'fecha_comunicado'])
            ->where('visibilidad', 'P')
            ->where('fecha_comunicado', '<', $comunicado->fecha_comunicado)->orderBy('fecha_comunicado', 'desc')->first();
        }

        return Inertia::render('Comunicados/Comunicado', [
            'comunicado' => $comunicado,
            'siguiente' => $siguiente ?? null,
            'anterior' => $anterior ?? null
        ])
            ->withViewData(SEO::from($comunicado));
    }


    /**
     * Genera un PDF desde los datos de un comunicado
     */
    public function pdf($id)
    {
        if (is_numeric($id)) {
            $contenido = Comunicado::findOrFail($id);
        } else {
            $contenido = Comunicado::where('slug', $id)->firstOrFail();
        }

        $borrador = request()->has('borrador');
        $publicado = $contenido->visibilidad == 'P';
        $editor = optional(auth()->user())->can('administrar contenidos');
        if (!$contenido || (!$publicado && !$borrador && !$editor)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return $contenido->generatePdf();
    }




    /*
        public function archive(Request $request)
        {
            $buscar = $request->input('buscar');

            $comunicados = Comunicado::select(['slug', 'titulo', 'descripcion', 'fecha_comunicado'])
                ->where('visibilidad', 'P')
                ->latest()->paginate(10)->appends(['buscar' => $buscar]);

            return Inertia::render('Comunicados/Archivo', [
                'listado' => $comunicados
            ]);
        }*/


    public function procesar()
    {
        // comprueba en los comunicados si hay audios que aun no se han convertido, o si hay pdf que no se han preparado con sus metadatos
        $comunicados = Comunicado::where('audios', 'LIKE', '%upload%');

        foreach ($comunicados->get() as $comunicado) {
            if ($comunicado->audios) {
                // dd($comunicado);

                $año = date('Y', strtotime($comunicado->fecha_comunicado));
                $folder = "medios/comunicados/audios/$año";

                dispatch(new ProcesarAudios(Comunicado::class, $comunicado->id, $folder));
            }
        }
    }
}
