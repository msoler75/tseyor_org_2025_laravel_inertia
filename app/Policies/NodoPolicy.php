<?php

namespace App\Policies;

use App\Models\Acl;
use App\Models\Nodo;
use App\Models\User;


/**
 * Esta clase simula los permisos de linux con usuarios y grupos.
 * Un nodo es un archivo o una carpeta.
 * Podemos comprobar los permisos básicos (leer, escribir, ejecutar, o rwx).
 * Además de los permisos básicos, está el ACL (Access Control List) con el modelo Acl.
 */
class NodoPolicy
{

    /**
     * funciones básicas para comprobar permisos. Consulta la ACL
     */
    public function leer(?User $user, Nodo $nodo, ?Acl $acl = null): bool
    {
        return $this->permisoNodo($user, $nodo, 0b100) ? true : ($acl ? $this->tieneAcceso($nodo, $acl, 'leer') : false);
    }

    public function escribir(?User $user, Nodo $nodo, ?Acl $acl = null): bool
    {
        return $this->permisoNodo($user, $nodo, 0b010) ? true : ($acl ? $this->tieneAcceso($nodo, $acl, 'escribir') : false);
    }

    public function ejecutar(?User $user, Nodo $nodo, ?Acl $acl = null): bool
    {
        return $this->permisoNodo($user, $nodo, 0b001) ? true : ($acl ? $this->tieneAcceso($nodo, $acl, 'ejecutar') : false);
    }



    /**
     * Usa la máscara de bits para saber si hay permisos para este nodo
     */
    public function permisoNodo(?User $user, Nodo $nodo, int $bits): bool
    {
        try {
            if (!$nodo) {
                return false;
            }

            $permisos = octdec($nodo->permisos);

            if ($user) {
                // Verificar el permiso del propietario (owner)
                if ($nodo->user_id && $nodo->user_id === $user->id && ($permisos & ($bits << 6)) !== 0) {
                    return true;
                }

                // Verificar el permiso del grupo
                if ($nodo->group_id && $user->grupos()->where('grupos.id', $nodo->group_id)->isNotEmpty() && ($permisos & ($bits << 3)) !== 0) {
                    return true;
                }
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
     * Comprueba con la ACL si tiene el acceso a un nodo en concreto
     */
    public static function tieneAcceso(Nodo $nodo, Acl $acl, string $verbo = null)
    {
        // filtramos por verbo
        if ($verbo)
            $acl = $acl->filter(function ($nodo) use ($verbo) {
                return strpos($nodo->verbos, $verbo) !== false;
            });

        // tiene acceso global para todos los nodos?
        if ($acl->whereNull('nodo_id')->count() > 0)
            return true;

        // tiene acceso a este nodo?
        if ($acl->where('nodo_id', $nodo->id)->count() > 0)
            return true;

        // tiene acceso a una carpeta padre?

        // parece que los LIKE no funcionan aquí:
        //if ($acl->where("'$nodo->ruta'", 'LIKE', "CONCAT(ruta, '%')")->count() > 0)
        //  return true;

        foreach ($acl->toArray() as $registro) {
            if (strpos($nodo->ruta, $registro['ruta']) === 0) {
                return true;
            }
        }

        return false;
    }


}
