<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Informe;
use App\Models\Equipo;
use App\Pigmalion\SEO;
use App\Pigmalion\Busquedas;

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

    public function equipo(Request $request, $equipo_slug)
    {
        $buscar = $request->input('buscar');
        $categoria = $request->input('categoria');
        return $this->listar($buscar, $categoria, $equipo_slug);
    }

    private function listar($buscar, $categoria, $equipo_id_slug)
    {
        if ($equipo_id_slug)
            $equipo = is_numeric($equipo_id_slug) ? Equipo::find($equipo_id_slug) : Equipo::where('slug', $equipo_id_slug)->first();

        // devuelve los items recientes segun la busqueda
        if ($buscar) {
            $resultados = Informe::search($buscar);
        } else {
            // obtiene los items sin busqueda
            $resultados = Informe::select(['id', 'titulo', 'descripcion', 'updated_at', 'categoria'])
                ->where('visibilidad', 'P');
        }

        // parámetros
        if ($categoria)
            $resultados = $resultados->where('categoria', $categoria);

        if ($equipo)
            $resultados = $resultados->where('equipo_id', $equipo->id);

        $resultados = $resultados
            ->paginate(12)
            ->appends(['buscar' => $buscar,  'categoria' => $categoria, 'equipo' => $equipo->id]);

        if ($buscar)
            Busquedas::formatearResultados($resultados, $buscar);

        $categorias = (new Informe())->getCategorias();



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
