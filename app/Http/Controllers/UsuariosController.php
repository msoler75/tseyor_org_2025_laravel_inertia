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
    public static $ITEMS_POR_PAGINA = 50;

    public function index(Request $request)
    {
        $page = $request->input('page', 1);
        $buscar = $request->input('buscar');

        $query = User::select(['id', 'name as nombre', 'slug', 'frase', 'created_at']);

        if ($buscar) {
            $ids = User::search($buscar)->get()->pluck('id')->toArray();
            $query->whereIn('users.id', $ids);
        } else
            $query->latest();

        $resultados = $query->paginate(self::$ITEMS_POR_PAGINA, ['*'], 'page', $page)
            ->appends($request->except('page'));

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
    public function store(Request $request, $id)
    {
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
        foreach ($resultados as $idx => $u)
            $resultados[$idx]['nombre'] = $u['name'];

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
    public function grupos()
    {
        $grupos = Grupo::get();
        return response()->json($grupos, 200);
    }
}
