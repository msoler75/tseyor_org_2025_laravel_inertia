<?php

namespace App\Policies;

// use App\Models\Acl;
use App\Models\Nodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
//use Illuminate\Support\Facades\Cache;

/**
 * Esta clase simula los permisos de linux con usuarios y grupos.
 * Un nodo es un archivo o una carpeta.
 * Podemos comprobar los permisos básicos (leer, escribir, ejecutar, o rwx).
 * Además de los permisos básicos, está el ACL (Access Control List) con el modelo Acl.
 *
 * Nota: El sticky bit no se gestiona aquí. Por tanto debe controlarse en otros controladores.
 *
 *
 * Linux permision
 * --------------
 *
 * Linux file ownership
 * --------------------
 * In Linux, there are three types of owners: user, group, and others .
 *
 * -Linux User
 * A user is the default owner and creator of the file. So this user is called owner as well.
 *
 * -Linux Group
 * A user-group is a collection of users. Users that belonging to a group will have the same Linux group permissions to access a file/ folder.
 *
 * You can use groups to assign permissions in a bulk instead of assigning them individually. A user can belong to more than one group as well.
 *
 * -Other
 * Any users that are not part of the user or group classes belong to this class.
 *
 * Linux File Permissions
 * ----------------------
 *
 * File permissions fall in three categories: read, write, and execute.
 *
 * -Read permission
 * For regular files, read permissions allow users to open and read the file only. Users can't modify the file.
 *
 * Similarly for directories, read permissions allow the listing of directory content without any modification in the directory.
 *
 * -Write permission
 * When files have write permissions, the user can modify (edit, delete) the file and save it.
 *
 * For folders, write permissions enable a user to modify its contents (create, delete, and rename the files inside it), and modify the contents of files that the user has write permissions to.
 *
 * -Execute permission
 * For files, execute permissions allows the user to run an executable script.
 *
 * For directories, the user can access them, and access details about files in the directory.
 **/

class NodoPolicy
{

    /**
     * funciones básicas para comprobar permisos.
     */
    public function leer(?User $user, Nodo $nodo): bool
    {
        return $this->permisoNodo($user, $nodo, 0b100) ? true : $nodo->tieneAcceso($user, 'leer');
    }

    public function escribir(?User $user, Nodo $nodo): bool
    {
        return $this->permisoNodo($user, $nodo, 0b010) ? true : $nodo->tieneAcceso($user, 'escribir');
    }

    public function ejecutar(?User $user, Nodo $nodo): bool
    {
        return $this->permisoNodo($user, $nodo, 0b001) ? true : $nodo->tieneAcceso($user, 'ejecutar');
    }


    /**
     * Usa la máscara de bits para saber si hay permisos de lectura para este nodo
     *
     * user group others
     * rwx  rwx   rwx
     * 876  543   210  (posición del bit)
     */
    public function permisoNodo(?User $user, Nodo $nodo, int $bits): bool
    {
        try {
            //if (!$nodo)
            //  return false;

            // Log::info("permisosNodo($bits) : $nodo->permisos");

            // elimino el sticky bit
            $permisos = octdec(strlen($nodo->permisos) > 3 ? substr($nodo->permisos, 1) : $nodo->permisos);

            // Log::info("permisos2 : $permisos");

            if ($user) {

                // Verificar el permiso del propietario (owner)
                if ($nodo->user_id && $nodo->user_id === $user->id && ($permisos & ($bits << 6)) !== 0)
                    return true;

                // Verificar el permiso del grupo
                if ($user && $nodo->group_id) {

                    // verificar si el permiso está concedido para el grupo
                    if (($permisos & ($bits << 3)) !== 0 && $user->enGrupo($nodo->group_id))
                        return true;
                }
            }

            // Log::info("$permisos & $bits: ".($permisos & $bits)." " . (($permisos & $bits) !== 0));
            // Verificar el permiso de otros (others)
            return ($permisos & $bits) !== 0;

        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            // La ruta no existe o hay un error al obtener los metadatos
            return false;
        }
    }
}
