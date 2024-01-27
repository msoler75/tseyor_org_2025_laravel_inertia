<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;

class DashboardController {

    public function index() {
        $users = User::select()->latest()->take(5)->get();
        return view('admin-dashboard', ['users'=>$users]);
    }
}
