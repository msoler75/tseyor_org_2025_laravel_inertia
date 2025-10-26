<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckIfAdmin
{
    /**
     * Checked that the logged in user is an administrator.
     *
     * --------------
     * VERY IMPORTANT
     * --------------
     * If you have both regular users and admins inside the same table, change
     * the contents of this method to check that the logged in user
     * is an admin, and not a regular user.
     *
     * Additionally, in Laravel 7+, you should change app/Providers/RouteServiceProvider::HOME
     * which defines the route where a logged in user (but not admin) gets redirected
     * when trying to access an admin route. By default it's '/home' but Backpack
     * does not have a '/home' route, use something you've built for your users
     * (again - users, not admins).
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable|null  $user
     * @return bool
     */
    private function checkIfUserIsAdmin($user)
    {
        // Verificar si el usuario tiene algún rol administrativo
        if (!$user) {
            return false;
        }

        // Convertir a instancia de User del modelo específico
        $userModel = \App\Models\User::find($user->getAuthIdentifier());

        if (!$userModel) {
            return false;
        }

        // Verificar roles administrativos usando Spatie Permission
        if($userModel->hasAnyRole([
            'superadministrador',
            'administrador',
            'secretaria',
            'comunicador',
            'editor'
        ])) {
            return true;
        }

        // Verificar permisos específicos
        if($userModel->hasAnyPermission([
            'administrar usuarios',
            'administrar contenidos',
            'administrar equipos',
            'administrar archivos',
            'administrar directorio',
            'administrar social',
            'avanzado',
            'administrar experiencias',
            'administrar legal',
            'coordinar equipo'
        ])) {
            return true;
        }

        return false;
    }

    /**
     * Answer to unauthorized access request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    private function respondToUnauthorizedRequest($request)
    {
    Log::info("CheckIfAdmin middleware: unauthorized access attempt");
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            // return redirect()->guest(backpack_url('login'));
            Log::info("CheckIfAdmin middleware: redirecting to admin login");
            return redirect()->guest('/admin/login?to=' . urlencode($request->fullUrl()));
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Determine which guard Backpack should use; fall back to app default ('web')
    $guardName = config('backpack.base.guard') ?: config('auth.defaults.guard', 'web');

    Log::info("CheckIfAdmin middleware auth guard=".$guardName);
    Log::info("CheckIfAdmin middleware user=".(Auth::guard($guardName)->user() ? Auth::guard($guardName)->user()->name : 'null'));

        if (Auth::guard($guardName)->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        if (! $this->checkIfUserIsAdmin(Auth::guard($guardName)->user())) {
            return $this->respondToUnauthorizedRequest($request);
        }

        return $next($request);
    }
}
