<?php

namespace App\Http\Middleware;

use App\Pigmalion\DeployHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CheckAllowedIP
{
    public function handle(Request $request, Closure $next)
    {
        $sessionToken = $request->header('X-Deploy-Session-Token');
        if ($sessionToken && Cache::get('deploy_session_token') === $sessionToken) {
            return $next($request);
        }

        try {
            DeployHelper::checkAllowedIP();
        } catch (\Exception $e) {
            return response()->json(['message' => 'IP no autorizada'], 403);
        }

        return $next($request);
    }
}
