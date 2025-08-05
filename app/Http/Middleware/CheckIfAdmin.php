<?php

namespace App\Http\Middleware;

use Closure;

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
        if ($request->ajax() || $request->wantsJson()) {
            return response(trans('backpack::base.unauthorized'), 401);
        } else {
            // return redirect()->guest(backpack_url('login'));
            return redirect()->guest('/login');
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
        if (backpack_auth()->guest()) {
            return $this->respondToUnauthorizedRequest($request);
        }

        if (! $this->checkIfUserIsAdmin(backpack_user())) {
            return $this->respondToUnauthorizedRequest($request);
        }

        return $next($request);
    }
}
