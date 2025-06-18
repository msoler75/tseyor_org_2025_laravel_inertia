<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class McpTokenController extends Controller
{
    /**
     * Muestra la vista para generar el token MCP.
     */
    public function show()
    {
        return inertia('Profile/McpToken');
    }

    /**
     * Genera y muestra el token JWT MCP para el usuario autenticado.
     */
    public function generate(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'iat' => time(),
            'exp' => time() + 60 * 60 * 24 * 30, // 30 días
        ];
        $key = config('mcp-server.jwt_secret_prefix') . config('app.key');
        $jwt = JWT::encode($payload, $key, 'HS256');

        return response()->json(['token' => $jwt]);
    }
}
