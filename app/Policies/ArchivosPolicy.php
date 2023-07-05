<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Carpeta;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ArchivosPolicy
{


    /* funciones básicas de permisos */

    public function leer(?User $user, string $ruta): bool
    {
        return $this->puede($user, $ruta, 'leer',     0b100);
    }

    public function escribir(?User $user, string $ruta): bool
    {
        return $this->puede($user, $ruta, 'escribir', 0b010);
    }

    public function ejecutar(?User $user, string $ruta): bool
    {
        return $this->puede($user, $ruta, 'ejecutar', 0b001);
    }



    /**
     * Métodos auxiliares
     */
    public function obtenerCarpeta($ruta)
    {
        //$carpeta = Carpeta::where('ruta', $ruta)->first();
        $carpeta = Carpeta::select(['carpetas.*', 'teams.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('teams', 'teams.id', '=', 'team_id')
            ->whereRaw("'$ruta' LIKE CONCAT(ruta, '%')")
            ->orderByRaw('LENGTH(ruta) DESC')
            ->first();

        return $carpeta;
    }

    private function permisoAdicional(?User $user, Carpeta $carpeta, string $accion): bool
    {
        $user_id = $user ? $user->id : -1;
        $equipos_ids = $user ? $user->equipos()->pluck('id') : [];

        $permiso = Permiso::join('carpetas', 'permisos.modelo_id', '=', 'carpetas.id')
            ->where('modelo', 'Carpeta')
            ->whereIn('modelo_id', $carpeta->pluck('id'))
            ->whereRaw("'$carpeta->ruta' LIKE CONCAT(carpetas.ruta, '%')")
            ->where(function ($query) use ($equipos_ids, $user_id) {
                $query->whereIn('permisos.team_id', $equipos_ids)
                    ->orWhere('permisos.user_id', $user_id);
            })
            ->where('accion', $accion)
            ->orderByRaw('LENGTH(carpetas.ruta) DESC')
            ->limit(1)
            ->exists();

        return $permiso;
    }

    private function puede(?User $user, string $ruta, string $accion, int $bits): bool
    {
        $carpeta = $this->obtenerCarpeta($ruta);

        if (!$carpeta) {
            return false;
        }

        $permisos = intval($carpeta->permisos ?? 0, 8);

        // Verificar el permiso del propietario (owner)
        if ($user && $carpeta->user_id && $carpeta->user_id === optional($user)->id && ($permisos & ($bits << 6)) !== 0) {
            return true;
        }

        // Verificar el permiso del grupo
        if ($user && $carpeta->team_id && $user->equipos()->where('id', $carpeta->team_id)->exists() && ($permisos & ($bits << 3)) !== 0) {
            return true;
        }

        // Verificar el permiso de otros (others)
        if (($permisos & $bits) !== 0) {
            return true;
        }

        return $this->permisoAdicional($user, $carpeta, $accion);
    }
}
