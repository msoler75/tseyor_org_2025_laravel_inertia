<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Comentario;

class AdminController {

    public function dashboard() {
        $users = User::select()->latest()->take(10)->get();


        $comentarios = Comentario::select()->with('user')->latest()->take(10)->get();

        // dd($comentarios);
        return view('admin.dashboard', ['users'=>$users, 'comentarios'=>$comentarios]);
    }

}
