<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Nodo;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;


/**
 * El nodos simula los permisos de linux con usuarios y grupos
 * Este clase sirve para gestionar los permisos de acceso a un elemento de nodos, que permisoNodo ser un archivo o una carpeta
 */
class LinuxPolicy
{


    /* funciones básicas de permisos */

    public function leer(Nodo $nodo, ?User $user, $acl = NULL): bool
    {
        $concedido = $this->permisoNodo($nodo, $user, 0b100);

        if ($concedido) return true;

        return $acl ? $this->tieneAcceso($nodo, $acl, 'leer') : false;
    }

    public function escribir(Nodo $nodo, ?User $user, $acl = NULL): bool
    {
        $concedido = $this->permisoNodo($nodo, $user, 0b010);

        if ($concedido) return true;

        return $acl ? $this->tieneAcceso($nodo, $acl, 'escribir') : false;
    }

    public function ejecutar(Nodo $nodo, ?User $user, $acl = NULL): bool
    {
        $concedido = $this->permisoNodo($nodo, $user, 0b001);

        if ($concedido) return true;

        return $acl ? $this->tieneAcceso($nodo, $acl, 'ejecutar') : false;
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

    private function permisoNodo(Nodo $nodo, ?User $user, int $bits): bool
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
        } catch (\Exception $e) {
            // La ruta no existe o hay un error al obtener los metadatos
            return false;
        }
    }


    /**
     * Access Control List. Devuelve un listado de permisos para este usuario
     */
    public function acl(?User $user, string $accion)
    {
        $user_id = $user ? $user->id : -1;
        $grupos_ids = $user ? $user->grupos()->pluck('grupos.id') : [];

        return Permiso::select('permisos.*', 'nodos.ruta')
            ->leftJoin('nodos', 'permisos.modelo_id', '=', 'nodos.id')
            ->where('modelo', 'nodos')
            ->where(function ($query) use ($grupos_ids, $user_id) {
                $query
                    ->where('permisos.user_id', $user_id)
                    ->orWhereIn('permisos.group_id', $grupos_ids);
            })
            ->where('accion', $accion)
            ->get();
    }


    /**
     * Comprueba con la ACL si tiene el acceso a un nodo en concreto
     */
    public function tieneAcceso(Nodo $nodo, $acl)
    {
        // tiene acceso global para todos los nodos?
        if ($acl->whereNull('modelo_id')->count() > 0)
            return true;

        // tiene acceso a este nodo?
        if ($acl->where('modelo_id', $nodo->id)->count() > 0)
            return true;

        // tiene acceso a una carpeta padre?
        if ($acl->where("'$nodo->ruta' LIKE CONCAT(ruta, '%')")->count() > 0)
            return true;

        return false;
    }
}
