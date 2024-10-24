<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use App\Models\Informe;
use App\Models\Equipo;
use App\Pigmalion\SEO;
use App\Pigmalion\BusquedasHelper;
use Illuminate\Support\Facades\Gate;

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
        $equipo = $equipo_id_slug ? (is_numeric($equipo_id_slug) ? Equipo::find($equipo_id_slug) : Equipo::where('slug', $equipo_id_slug)->first()) : null;

        $campos = ['informes.id', 'informes.titulo', 'informes.descripcion', 'informes.updated_at', 'informes.categoria', 'informes.visibilidad',
                  'equipos.id as equipo_id', 'equipos.nombre as nombre_equipo', 'equipos.slug as slug_equipo', 'equipos.oculto as equipo_oculto'];

        $resultados = Informe::select($campos)
            ->join('equipos', 'informes.equipo_id', '=', 'equipos.id');

        // busqueda
        if ($buscar) {
            $resultadosBusqueda = Informe::search($buscar)->get();
            $ids = $resultadosBusqueda->pluck('id')->toArray();
            $resultados->whereIn('informes.id', $ids);
        }

        if ($equipo) {
            $resultados->where('equipo_id', $equipo->id);
        }

        if ($categoria) {
            $resultados->where('informes.categoria', 'LIKE', "%$categoria%");
        }

        if (Gate::denies('administrar equipos')) {
            $user = auth()->user();

            if ($user) {

                // solo informes publicados o borradores que puedo ver como coordinador
                $resultados->where(function ($query) use ($user) {
                    $query->where('informes.visibilidad', 'P')
                        ->orWhereIn('informes.equipo_id', function ($subquery) use ($user) {
                            $subquery->select('equipo_id')
                                ->from('equipo_user')
                                ->where('rol', 'coordinador')
                                ->where('user_id', $user->id);
                        });
                });

                // solo informes de equipos no privados o en los que soy miembro
                $resultados->where(function ($query) use ($user) {
                    $query->where('equipos.oculto', false)
                        ->orWhereIn('equipos.id', function ($subquery) use ($user) {
                            $subquery->select('equipo_id')
                                ->from('equipo_user')
                                ->where('user_id', $user->id);
                        });
                });
            } else {
                // si no tengo cuenta de usuario, solo puedo ver informes publicados, y que son de equipos no privados
                $resultados->where('visibilidad', 'P')
                    ->where('equipos.oculto', false);
            }
        }

        $categorias = (new Informe())->getCategorias($resultados->get());

        $resultados = $resultados->orderBy('informes.updated_at', 'desc')
            ->paginate(12)
            ->appends(['categoria' => $categoria, 'equipo' => $equipo_id_slug]);

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
        $informe = Informe::with('equipo')->findOrFail($id);

        $user = auth()->user();
        $preview = request()->has('borrador');
        $publicado =  $informe->visibilidad == 'P';
        $administrador = Gate::allows('administrar equipos');
        $equipo = $informe->equipo;
        $coordinador = $user && $equipo->esCoordinador(optional($user)->id);

        $miembro = $equipo->esMiembro(optional($user)->id);

        if (!$informe || (!$publicado && !$preview && !$coordinador && !$administrador) || ($equipo->oculto && !$miembro && !$coordinador && !$administrador)) {
            abort(404); // Item no encontrado o no autorizado
        }

        return Inertia::render('Informes/Informe', [
            'informe' => $informe,
            'equipo' => $equipo,
            'soyCoordinador' => $coordinador
        ])
            ->withViewData(SEO::from($informe));
    }
}
