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
class MuularElectronico
{

    /**
     * Redirige al portal de Muular Electrónico pasándoles los datos del usuario actual
     *
     * @throws \Error
     * @return mixed|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public static function redirigir()
    {
        $jwt = self::jwt();
        // si no ha iniciado sesión, debe redirigir a login
        if (!$jwt)
            return redirect()->route('login', ['to' => 'muular-electronico']);

        $url = config('app.muular_electronico.auth_url');

        if (!$url)
            throw new \Error("Error de configuración");

        return view('redirect-to-muular-electronico', ['url' => $url, 'token' => $jwt]);
    }


    /**
     * Obtiene el saldo del usuario actual
     * @throws \Error
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public static function saldo()
    {
        $jwt = self::jwt();

        if (!$jwt)
            return response()->json(['error' => 'No ha iniciado sesión']);

        $url = config('app.muular_electronico.saldo_url');

        if (!$url)
            throw new \Error("Error de configuración");

        // llamamos a la URL con curl, pasandole mediante POST el parámetro 'token' que es el $jwt y recogemos el resultado en JSON

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['token' => $jwt]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        Log::info("Llamada a MuularElectronico:saldo", ['respuesta' => $result]);
        // decodificamos la respuesta

        $json = json_decode($result, true);

        return response()->json($json);
    }


    /**
     * Crea el token JWT con los valores del usuario actual
     *
     * @param array $extra valores adicionales para el payload
     * @throws \Error
     * @return string|null
     */
    private static function jwt(array $extra = null)
    {
        // obtiene el usuario actual
        $user = auth()->user();
        if (!$user)
            return null;

        // Generar el token JWT
        $key = config('app.muular_electronico.jwt_secret');

        if (!$key)
            throw new \Error("Error de configuración. JWT key no definido");

        $payload = [
            'id' => $user->id,
            'user' => $user->name,
            'email' => $user->email,
            'expira' => time() + 60 * 10 // Expira en 10 minutos
        ];

        if ($extra)
            $payload = array_merge($payload, $extra);

        $jwt = JWT::encode($payload, $key, 'HS256');

        return $jwt;
    }



    /**
     * API para verificar la contraseña desde el muular electrónico
     *
     * @param \App\Services\Request $request
     * @return void
     */
    public function check_password() {
        $token = isset($_POST["token"]) ? $_POST["token"] : "";
        if (!$token)
            return response()->json(['error'=>'Token no especificado']);

        $key = config('app.muular_electronico.jwt_secret');

        if (!$key)
            throw new \Error("Error de configuración. JWT key no definido");

        $payload = JWT::decode($token, new Key($key, 'HS256'));

        if (!$payload)
            return response()->json(['error'=>'Error en los datos']);

        Log::info("Muular electrónico. Comprobación de contraseña", ['payload' => $payload]);

        $username = $payload->user;
        $email = $payload->email;
        $passwordIntento = $payload->password;

        $user = User::select('id', 'name', 'email', 'password')->where('name', $username)->orWhere('email', $email)->firstOrFail();

        $user->makeVisible('password');

        $password = $user->getAuthPassword();

        Log::info("user data is", ['password'=>$password, 'user'=>$user->toArray()]);
        // comprobamos la contraseña para este usuario
        if(Hash::check($passwordIntento, $password))
        // if(password_verify($passwordIntento, $user->password))
            return response()->json(['ok'=>1, 'mensaje'=>'Contraseña válida']);

        return response()->json(['error'=>'Contraseña incorrecta']);
    }
}
