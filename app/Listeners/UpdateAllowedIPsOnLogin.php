<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Pigmalion\DeployHelper;

class UpdateAllowedIPsOnLogin
{
    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        try {
            Log::debug('UpdateAllowedIPsOnLogin listener invoked', ['user' => $user ? $user->email ?? null : null]);

            // Verificar que sea instancia de User y que el nombre sea "admin"
            if (!$user instanceof User || $user->name !== 'admin') {
                Log::debug('UpdateAllowedIPsOnLogin: user is not admin (name != "admin")', ['user' => $user ? $user->name ?? null : null]);
                return;
            }

            $ip = Request::ip();

            // Agregar IP usando DeployHelper
            DeployHelper::addAllowedIP($ip, $user->email);

            Log::info("IP agregada a lista de IPs permitidas", [
                'ip' => $ip,
                'user' => $user->email
            ]);
        } catch (\Throwable $e) {
            // Registrar cualquier excepción para diagnosticar por qué no se actualiza el archivo
            Log::error('UpdateAllowedIPsOnLogin exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => $user ? $user->email ?? null : null,
            ]);
        }
    }
}
