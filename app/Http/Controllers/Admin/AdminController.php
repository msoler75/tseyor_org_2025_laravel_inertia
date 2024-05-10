<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Comentario;
use App\Models\Contenido;
use App\Models\Revision;
use App\Models\Busqueda;
use Illuminate\Support\Facades\DB;

class AdminController
{

    public function dashboard()
    {
        $users_creados = User::select()->latest()->take(10)->get();

        $users_activos = DB::table('sessions')
            ->select('users.name as name', 'users.slug as slug', 'user_id', 'last_activity')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->orderBy('last_activity', 'desc')
            ->take(10)
            ->get()->toArray();

        $comentarios = Comentario::select()->with('user')->latest()->take(10)->get();

        $contenidos_creados = Contenido::select()->orderBy('created_at', 'desc')->take(10)->get();

        $contenidos_modificados = Contenido::select()->orderBy('updated_at', 'desc')->take(10)->get();

        $revisiones = Revision::select()->latest()->take(10)->get();

        $busquedas = Busqueda::select(['query', 'created_at'])->latest()->take(10)->get();

        $data = [
            'users_creados' => $users_creados,
            'users_activos' => $users_activos,
            'comentarios' => $comentarios,
            'contenidos_creados' => $contenidos_creados,
            'contenidos_modificados' => $contenidos_modificados,
            'revisiones' => $revisiones,
            'busquedas' => $busquedas
        ];
        // dd($revisiones);

        // dd($comentarios);
        return view('admin.dashboard', $data);
    }

}
