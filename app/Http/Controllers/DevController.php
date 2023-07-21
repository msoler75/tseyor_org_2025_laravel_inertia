<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class DevController extends Controller
{
public function loginUser1()
    {
        $user = User::find(3);
        Auth::login($user); // Autenticar al usuario 1
        return response()->json(['message' => 'usuario cambiado'], 200);
    }

    public function loginUser2()
    {
        $user = User::find(2); // Obtener usuario 2 de la base de datos
        Auth::login($user); // Autenticar al usuario 2
        return response()->json(['message' => 'usuario cambiado'], 200);
    }
}
