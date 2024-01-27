<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

class AdminController {

    public function dashboard() {
        $users = User::select()->latest()->take(5)->get();
        return view('admin.dashboard', ['users'=>$users]);
    }

}
