<?php

namespace App\Services;

// use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * Convierte el audio a un formato mp3 de menos peso
 */
class MuularElectronico
{
    /**
     * Redirige al portal de Muular Electrónico pasándoles los datos del usuario actual
     *
     * @return mixed|Factory|View|RedirectResponse
     *
     * @throws \Error
     */
    public static function redirigir()
    {
        $from = $_GET['from'] ?? '';

        $jwt = self::jwt();
        // si no ha iniciado sesión o el servicio no está configurado, avisar y volver
        if (! $jwt) {
            \Illuminate\Support\Facades\Log::warning('MuularElectronico: no disponible (JWT no configurado o clave inválida)');
            return redirect()->route('miembros')->with('message', 'Muular electrónico no disponible en este momento.');
        }

        $auth_url = config('app.muular_electronico.auth_url');

        if (! $auth_url) {
            Log::warning('MuularElectronico: auth_url no configurada');
            return redirect()->route('login');
        }

        return view('redirect-with-jwt', ['url' => $auth_url.($from ? '?to='.$from : ''), 'token' => $jwt]);
    }

    /**
     * Obtiene el saldo del usuario actual
     *
     * @return mixed|JsonResponse
     *
     * @throws \Error
     */
    public static function saldo()
    {
        $jwt = self::jwt();

        if (! $jwt) {
            \Illuminate\Support\Facades\Log::warning('MuularElectronico: saldo no disponible (JWT no configurado o clave inválida)');
            return response()->json(['error' => 'Muular electrónico no disponible']);
        }

        $url = config('app.muular_electronico.saldo_url');

        if (! $url) {
            Log::warning('MuularElectronico: saldo_url no configurada');
            return response()->json(['error' => 'Servicio de muular no configurado']);
        }

        // llamamos a la URL con curl, pasándole mediante POST el parámetro 'token' que es el $jwt y recogemos el resultado en JSON

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ['token' => $jwt]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $result = curl_exec($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($result === false || $curlError) {
            Log::warning('MuularElectronico: error de conexión al obtener saldo', ['url' => $url, 'error' => $curlError]);
            return response()->json(['error' => 'Muular electrónico no disponible']);
        }

        Log::info('Llamada a MuularElectronico:saldo', ['url' => $url, 'respuesta' => $result]);
        // decodificamos la respuesta

        $json = json_decode($result, true);

        if (! is_array($json)) {
            Log::warning('MuularElectronico: respuesta inválida', ['url' => $url, 'respuesta' => $result]);
            return response()->json(['error' => 'Muular electrónico no disponible']);
        }

        return response()->json($json);
    }

    /**
     * Crea el token JWT con los valores del usuario actual
     *
     * @param  array  $extra  valores adicionales para el payload
     * @return string|null
     *
     * @throws \Error
     */
    private static function jwt(?array $extra = null)
    {
        // obtiene el usuario actual
        $user = auth()->user();
        if (! $user) {
            return null;
        }

        // Generar el token JWT
        $key = config('app.muular_electronico.jwt_secret');

        if (! $key) {
            Log::warning('MuularElectronico: JWT key no definida en config');
            return null;
        }

        // Firebase JWT requiere al menos 32 bytes para HS256
        if (strlen($key) < 32) {
            Log::warning('MuularElectronico: JWT key demasiado corta ('.strlen($key).' bytes; mínimo 32)');
            return null;
        }

        $payload = [
            'id' => $user->id,
            'user' => $user->name,
            'email' => $user->email,
            'expira' => time() + 60 * 10, // Expira en 10 minutos
        ];

        if ($extra) {
            $payload = array_merge($payload, $extra);
        }

        try {
            $jwt = JWT::encode($payload, $key, 'HS256');
        } catch (\Throwable $e) {
            Log::warning('MuularElectronico: error al generar JWT: '.$e->getMessage());
            return null;
        }

        return $jwt;
    }

    /**
     * API para verificar la contraseña desde el muular electrónico
     *
     * @param  Request  $request
     * @return void
     */
    public function check_password()
    {
        $token = isset($_POST['token']) ? $_POST['token'] : '';
        if (! $token) {
            return response()->json(['error' => 'Token no especificado']);
        }

        $key = config('app.muular_electronico.jwt_secret');

        if (! $key) {
            return response()->json(['error' => 'Servicio de muular no configurado']);
        }

        $payload = JWT::decode($token, new Key($key, 'HS256'));

        if (! $payload) {
            return response()->json(['error' => 'Error en los datos']);
        }

        Log::info('Muular electrónico. Comprobación de contraseña', ['payload' => $payload]);

        $username = $payload->user;
        $email = $payload->email;
        $passwordIntento = $payload->password;

        $user = User::select('id', 'name', 'email', 'password')->where('name', $username)->orWhere('email', $email)->firstOrFail();

        $user->makeVisible('password');

        $password = $user->getAuthPassword();

        Log::info('user data is', ['password' => $password, 'user' => $user->toArray()]);
        // comprobamos la contraseña para este usuario
        if (Hash::check($passwordIntento, $password)) {
            // if(password_verify($passwordIntento, $user->password))
            return response()->json(['ok' => 1, 'mensaje' => 'Contraseña válida']);
        }

        return response()->json(['error' => 'Contraseña incorrecta']);
    }
}
