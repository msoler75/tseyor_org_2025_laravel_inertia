<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Equipo;
use App\Models\Comentario;
use App\Pigmalion\SEO;

class UsuariosController extends Controller
{

    public function index(Request $request)
    {
        $filtro = $request->input('buscar');

        $resultados = ($filtro ? User::where('slug', 'like', '%' . $filtro . '%')
            ->orWhere('name', 'like', '%' . $filtro . '%')
            ->orWhere('email', 'like', '%' . $filtro . '%')
            ->paginate(10)->appends(['buscar' => $filtro])
            :
            User::latest()->paginate(50)
        );

        return Inertia::render('Usuarios/Index', [
            'filtrado' => $filtro,
            'listado' => $resultados,
        ])
            ->withViewData(SEO::get('usuarios'));
    }

    public function show($id)
    {
        if (is_numeric($id)) {
            $usuario = User::with('equipos')->findOrFail($id);
        } else {
            $usuario = User::with('equipos')->where('slug', $id)->firstOrFail();
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

        $administrar = true;

        $equipos = $administrar ? Equipo::select('id', 'slug', 'nombre')->get() : [];

        return Inertia::render('Usuarios/Usuario', [
            'usuario' => $usuario,
            'comentarios' => $comentarios,
            'equipos' => $equipos,
            'administrar' => $administrar
        ])
            ->withViewData(SEO::from($usuario));
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

            $resultados = User::select(['id', 'name as nombre'])
            ->search($buscar)->take(10)->get()->toArray();

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
}
