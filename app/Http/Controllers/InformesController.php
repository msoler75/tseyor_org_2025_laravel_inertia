<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Informe;
use App\Models\Equipo;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;

class InformesController extends Controller
{
    //
    public function index(Request $request)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        $equipo_id = $request->input('equipo');

        return $this->listar($buscar, $categoria, $equipo_id);
    }



    //
    public function equipo(Request $request, $equipo_slug)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        return $this->listar($buscar, $categoria, $equipo_slug);
    }


    //
    private function listar($buscar, $categoria, $equipo_id_slug)
    {
        $equipo = null;
        if ($equipo_id_slug)
            $equipo =  is_numeric($equipo_id_slug) ? Equipo::find($equipo_id_slug) : Equipo::where('slug', $equipo_id_slug)->first();

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Informe::search($buscar)
            // Agregamos la join y el select
            ->query(function ($query) {
                return $query->select(['informes.id', 'informes.titulo', 'informes.descripcion', 'informes.updated_at', 'informes.categoria', 'equipos.nombre as nombre_equipo', 'equipos.slug as slug_equipo'])
                ->join('equipos', 'informes.equipo_id', '=', 'equipos.id');
            });
        } else {
            // obtiene los items sin busqueda
            $resultados = Informe::select(['informes.id', 'informes.titulo', 'informes.descripcion', 'informes.updated_at', 'informes.categoria', 'equipos.nombre as nombre_equipo', 'equipos.slug as slug_equipo'])
                ->join('equipos', 'informes.equipo_id', '=', 'equipos.id')
                ->where('informes.visibilidad', 'P');
        }

        if ($equipo)
            $resultados = $resultados->where('equipo_id', $equipo->id);

        // obtiene las categorías según los resultados de búsqueda
        $categorias = (new Informe())->getCategorias($resultados->get());

        // parámetros
        if ($categoria)
            $resultados = $resultados->where('informes.categoria', 'LIKE', "%$categoria%");

        if (!$buscar)
            $resultados = $resultados->orderBy('informes.updated_at', 'desc');

        $resultados = $resultados
            ->paginate(12)
            ->appends(['buscar' => $buscar,  'categoria' => $categoria, 'equipo' => $equipo_id_slug]);

        if ($buscar)
            BusquedasHelper::formatearResultados($resultados, $buscar);

        return Inertia::render('Informes/Index', [
            'categoriaActiva' => $categoria,
            'filtrado' => $buscar,
            'listado' => $resultados,
            'categorias' => $categorias,
            'equipo' => $equipo
        ])
            ->withViewData(SEO::get('informes'));
    }



    public function show($id)
    {
        $informe = Informe::findOrFail($id);

        $user = auth()->user();
        $borrador = request()->has('borrador');
        $publicado =  $informe->visibilidad == 'P';
        $editor = optional($user)->can('administrar equipos');
        $coordinador = $user && $informe->equipo->esCoordinador(optional($user)->id);
        if (!$informe || (!$publicado && !$borrador && !$editor && !$coordinador)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Informes/Informe', [
            'informe' => $informe,
            'equipo' => $informe->equipo
        ])
            ->withViewData(SEO::from($informe));
    }
}
