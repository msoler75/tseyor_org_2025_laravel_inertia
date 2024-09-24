<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Grupo;
use App\Models\Comentario;
use App\Pigmalion\SEO;

class UsuariosController extends Controller
{

    public function index(Request $request)
    {
        $buscar = $request->input('buscar');

        $resultados = ($buscar ? User::where('slug', 'like', '%' . $buscar . '%')
            ->orWhere('name', 'like', '%' . $buscar . '%')
            ->orWhere('email', 'like', '%' . $buscar . '%')
            ->paginate(10)->appends(['buscar' => $buscar])
            :
            User::latest()->paginate(50)
        );

        return Inertia::render('Usuarios/Index', [
            'filtrado' => $buscar,
            'listado' => $resultados,
        ])
            ->withViewData(SEO::get('usuarios'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $usuario = User::with('equipos')->with('grupos')->findOrFail($id);
        } else {
            $usuario = User::with('equipos')->with('grupos')->where('slug', $id)->firstOrFail();
        }

        if (!$usuario) {
            abort(404); // usuario no encontrado
        }

        $comentarios = Comentario::join('users', 'comentarios.user_id', '=', 'users.id')
            ->where('users.id', $usuario->id)
            ->select('comentarios.*')
            ->orderBy('comentarios.created_at', 'desc') // Ordenar por fecha de creación descendente
            ->take(7)
            ->get();

        $administrar = false;

        $equipos = $administrar ? Equipo::select('id', 'slug', 'nombre')->get() : [];

        return Inertia::render('Usuarios/Usuario', [
            'usuario' => $usuario,
            'comentarios' => $comentarios,
            'equipos' => $equipos,
            'administrar' => $administrar
        ])
            ->withViewData(SEO::from($usuario));
    }

    // guardamos cambios en un usuario
    public function store(Request $request, $id) {
        $user = User::findOrFail($id);
        $user->update($request->all());
        return response()->json($user, 200);
    }


    ///////////////////////////////////////////////////////////////////////////////////////////////////
    // JSON

    /**
     * Busca usuarios y retorna lista de usuarios (solo 10 primeros)
     */
    public function search($buscar)
    {
        /*$resultados = User::select(['id', 'name as nombre'])
            ->where('slug', 'like', '%' . $buscar . '%')
            ->orWhere('name', 'like', '%' . $buscar . '%')
            ->orWhere('email', 'like', '%' . $buscar . '%')
            ->take(10)->get()->toArray();*/

            $resultados = User::search($buscar)->take(10)->get()->toArray();
            foreach($resultados as $idx => $u)
                $resultados[$idx]['nombre'] = $u['name'];

                // repeat values in array
                $resultados = array_values($resultados);
                if(count($resultados))
                for($i=0;$i<12;$i++) {
                    $resultados[] = $resultados[0];
                }

        return response()->json($resultados, 200);
    }

    /**
     * Retorna los permisos de usuario actual
     */
    public function permissions()
    {
        $user = auth()->user();
        $permisos = $user ? $user->getAllPermissions()->pluck('name') : [];
        return response()->json($permisos, 200);
    }

    /**
     * Retorna la lista de grupos
     */
    public function grupos() {
        $grupos = Grupo::get();
        return response()->json($grupos, 200);
    }
}
