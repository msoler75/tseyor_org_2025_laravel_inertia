<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 *
 */
class DevController extends Controller
{
    /**
     * Logs in a user.
     *
     * @return JsonResponse JSON response with a success message
     */
    public function loginUser1(): JsonResponse
    {
        $user = User::find(2);
        Auth::login($user); // Autenticar al usuario 3
        return response()->json(['message' => 'usuario cambiado'], 200);
    }

    public function loginUser2()
    {
        $user = User::find(3); // Obtener usuario 2 de la base de datos
        Auth::login($user); // Autenticar al usuario 2
        return response()->json(['message' => 'usuario cambiado'], 200);
    }
}
