<?php

namespace App\Services;

// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;
use Illuminate\Support\Facades\Hash;


/**
 * Convierte el audio a un formato mp3 de menos peso
 */
class TseyorCanva
{
    /**
     * Redirige al portal de Muular Electrónico pasándoles los datos del usuario actual
     *
     * @throws \Error
     * @return mixed|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public static function redirigir()
    {
        $from = $_GET['from'] ?? '';

        $jwt = self::jwt();
        // si no ha iniciado sesión, debe redirigir a login
        if (!$jwt)
            return redirect()->route('login',  [
                'to' => 'tseyor-canva' . ($from ? '?from=' . $from : '')
            ]);

        $auth_url = config('app.tseyor_canva.auth_url');

        if (!$auth_url)
            throw new \Error("Error de configuración");

        return view('redirect-with-jwt', ['url' => $auth_url. ($from ? '?to=' . $from : ''), 'token' => $jwt]);
    }


    /**
     * Crea el token JWT con los valores del usuario actual
     *
     * @param array $extra valores adicionales para el payload
     * @throws \Error
     * @return string|null
     */
    private static function jwt(array $extra=null)
    {
        // obtiene el usuario actual
        $user = auth()->user();
        if (!$user)
            return null;

        // Generar el token JWT
        $key = config('app.tseyor_canva.jwt_secret');

        if (!$key)
            throw new \Error("Error de configuración. JWT key no definido");

        $payload = [
            'id' => $user->id,
            'user' => $user->name,
            'email' => $user->email,
            'name' => $user->name,
            'image' => $user->profile_photo_url, // URL absoluta de la imagen de perfil
            'exp' => time() + 60 * 60 * 24 * 7// Expira en 7 días
        ];

        if ($extra)
            $payload = array_merge($payload, $extra);

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }

}
