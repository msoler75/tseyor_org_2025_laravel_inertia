<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDeployToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Deploy-Token');
        $expected = config('app.deploy_token');
        if (!$token || !$expected || !hash_equals($expected, $token)) {
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }
        return $next($request);
    }
}
