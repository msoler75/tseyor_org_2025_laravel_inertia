<?php

namespace App\Listeners;

use App\Models\User;
use App\Pigmalion\DeployHelper;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

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

            // Solo el superadministrador del sitio (User::esSuperAdmin)
            if (! $user instanceof User || ! $user->esSuperAdmin()) {
                Log::debug('UpdateAllowedIPsOnLogin: user is not superadmin', ['user' => $user ? $user->name ?? null : null]);

                return;
            }

            $ip = Request::ip();

            // Agregar IP usando DeployHelper
            DeployHelper::addAllowedIP($ip, $user->email);

            Log::info('IP agregada a lista de IPs permitidas', [
                'ip' => $ip,
                'user' => $user->email,
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
