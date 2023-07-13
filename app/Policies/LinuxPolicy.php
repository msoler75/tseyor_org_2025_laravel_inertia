<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Nodo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


/**
 * El nodos simula los permisos de linux con usuarios y grupos
 * Este clase sirve para gestionar los permisos de acceso a un elemento de nodos, que puede ser un archivo o una carpeta
 */
class LinuxPolicy
{


    /* funciones básicas de permisos */

    public function leer(Nodo $nodo, ?User $user): bool
    {
        return $this->puede($nodo, $user, 0b100, 'leer');
    }

    public function escribir(Nodo $nodo, ?User $user): bool
    {
        return $this->puede($nodo, $user, 0b010, 'escribir');
    }

    public function ejecutar(Nodo $nodo, ?User $user): bool
    {
        return $this->puede($nodo, $user, 0b001, 'ejecutar');
    }

    /**
     * Obtiene el item más cercano siendo el mismo o antecesor de la ruta
     */
    public function obtenerMejorNodo($ruta)
    {
        return Nodo::select(['nodos.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->whereRaw("'$ruta' LIKE CONCAT(nodos.ruta, '%')")
            ->orderByRaw('LENGTH(nodos.ruta) DESC')
            ->first();
    }

    /**
     * Obtiene todos los items de la carpeta, sin incluir el item de la carpeta
     */
    public function obtenerNodos($ruta)
    {
        return Nodo::select(['nodos.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->where('nodos.ruta', 'LIKE', $ruta . '/%')
            ->whereRaw("LENGTH(nodos.ruta) - LENGTH(REPLACE(nodos.ruta, '/', '')) = " . (substr_count($ruta, '/') + 1))
            ->orderByRaw('LENGTH(nodos.ruta) ASC')
            ->get();
    }

    private function puede(Nodo $nodo, ?User $user, int $bits, string $accion): bool
    {
        try {
            if (!$nodo) {
                return false;
            }

            $permisos = octdec($nodo->permisos);

            // Verificar el permiso del propietario (owner)
            if ($user && $nodo->user_id && $nodo->user_id === optional($user)->id && ($permisos & ($bits << 6)) !== 0) {
                return true;
            }

            // Verificar el permiso del grupo
            if ($user && $nodo->group_id && $user->grupos()->where('grupos.id', $nodo->group_id)->exists() && ($permisos & ($bits << 3)) !== 0) {
                return true;
            }

            // Verificar el permiso de otros (others)
            if (($permisos & $bits) !== 0) {
                return true;
            }

            return false;

            // return $this->permisoAdicional($user, $nodo, $accion);
        } catch (\Exception $e) {
            // La ruta no existe o hay un error al obtener los metadatos
            return false;
        }
    }



    private function permisoAdicional(?User $user, Nodo $nodo, string $accion): bool
    {
        // TO-DO??
        return false;

        $user_id = $user ? $user->id : -1;
        $grupos_ids = $user ? $user->grupos()->pluck('id') : [];

        $permiso = Permiso::join('nodos', 'permisos.modelo_id', '=', 'nodos.id')
            ->where('modelo', 'Carpeta')
            ->whereIn('modelo_id', $nodo->pluck('id'))
            ->whereRaw("'$nodo->ruta' LIKE CONCAT(nodos.ruta, '%')")
            ->where(function ($query) use ($grupos_ids, $user_id) {
                $query->whereIn('permisos.group_id', $grupos_ids)
                    ->orWhere('permisos.user_id', $user_id);
            })
            ->where('accion', $accion)
            ->orderByRaw('LENGTH(nodos.ruta) DESC')
            ->limit(1)
            ->exists();

        return $permiso;
    }
}
