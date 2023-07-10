<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
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
        $usuario = User::with('equipos')
            ->where('slug', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        if (!$usuario) {
            abort(404); // usuario no encontrado
        }

        $comentarios = Comentario::join('users', 'comentarios.user_id', '=', 'users.id')
            ->where('users.id', $usuario->id)
            ->select('comentarios.*')
            ->orderBy('comentarios.created_at', 'desc') // Ordenar por fecha de creación descendente
            ->take(7)
            ->get();

        return Inertia::render('Usuarios/Usuario', [
            'usuario' => $usuario,
            'comentarios' => $comentarios
        ])
            ->withViewData(SEO::from($usuario));
    }
}
