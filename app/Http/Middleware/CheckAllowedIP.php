<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Pigmalion\DeployHelper;

class CheckAllowedIP
{
    public function handle(Request $request, Closure $next)
    {
        try {
            DeployHelper::checkAllowedIP();
        } catch (\Exception $e) {
            return response()->json(['message' => 'IP no autorizada'], 403);
        }
        return $next($request);
    }
}
