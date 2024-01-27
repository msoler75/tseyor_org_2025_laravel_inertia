<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Comentario;
use Illuminate\Support\Facades\DB;

class AdminController {

    public function dashboard() {
        $users_creados = User::select()->latest()->take(10)->get();

        $users_activos = DB::table('sessions')
            ->select('users.name as name', 'users.slug as slug', 'user_id', 'last_activity')
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->orderBy('last_activity', 'desc')
            ->take(10)
            ->get()->toArray();

        $comentarios = Comentario::select()->with('user')->latest()->take(10)->get();

        // dd($comentarios);
        return view('admin.dashboard', ['users_creados'=>$users_creados, 'users_activos' =>$users_activos, 'comentarios'=>$comentarios]);
    }

}
