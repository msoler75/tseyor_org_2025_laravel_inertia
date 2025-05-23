<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckBoletinToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('X-Boletin-Token');
        $expected = config('app.boletin.token');
        if (!$token || !$expected || !hash_equals($expected, $token)) {
            \Log::channel('boletines')->warning('Acceso no autorizado al boletín', [
                'ip' => $request->ip(),
                'user_agent' => $request->header('User-Agent'),
                'token' => $token,
                'expected' => $expected,
            ]);
            return response()->json(['message' => 'Acceso no autorizado'], 403);
        }
        return $next($request);
    }
}
