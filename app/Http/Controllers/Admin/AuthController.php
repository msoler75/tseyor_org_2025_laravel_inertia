<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Perform login with the configured guard and regenerate session.
     *
     * @param User $user
     * @param bool $remember
     * @return void
     */
    private function performLogin(User $user, bool $remember = false)
    {
        $guard = config('backpack.base.guard') ?: config('auth.defaults.guard', 'web');
        Log::info("AuthController: performing login for user {$user->id} using guard={$guard}");
        Auth::guard($guard)->login($user, $remember);
        // No llamar session()->regenerate(): Auth::guard()->login() ya regenera la sesión internamente.
        // Llamarlo dos veces provoca que el navegador reciba un Set-Cookie con un session_id
        // que puede no coincidir, y además puede causar race conditions con location.reload().
    }

    public function showLoginForm()
    {
        return view('auth.admin-login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'loginName' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->loginName)
            ->orWhere('name', $request->loginName)
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            Log::info('AuthController: login success');
            $this->performLogin($user, $request->filled('remember'));
            $urlDestination = $request->input('to', '/admin/dashboard');
            Log::info('AuthController: redirecting to: ' . $urlDestination);
            return redirect()->intended($urlDestination);
        }

        return back()->withErrors(['loginName' => 'Credenciales incorrectas']);
    }



    /**
     * Se inicia sesión con el usuario deseado.
     * Para desarrollo y administración
     * @param mixed $idUser
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function loginAs($idUser)
    {
        $user = Auth::user();
        if (!$user || $user->id !== 1)
            return response()->json(['message' => 'Acceso no permitido'], 403);

        // cambiamos a nuevo usuario
        $user = User::find($idUser);
        if (!$user)
            return response()->json(['message' => 'usuario no encontrado'], 404);

        $this->performLogin($user, true); // Autenticar al usuario con el guard correcto

        // Devolvemos URL de redirect para que el frontend navegue con window.location.href.
        return response()->json([
            'redirect' => url()->previous(),
        ], 200);
    }

}
