<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Exige el superadministrador único del sitio (User::esSuperAdmin, name === 'admin').
 *
 * No confundir con CheckIfAdmin (Backpack), que da acceso al panel /admin a
 * cualquier rol editorial (superadministrador, administrador, secretaria,
 * comunicador, editor) o a quien tenga permisos "administrar *".
 */
class RequireSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()?->esSuperAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
