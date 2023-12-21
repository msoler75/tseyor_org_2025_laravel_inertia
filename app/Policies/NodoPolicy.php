<?php

namespace App\Policies;

// use App\Models\Acl;
use App\Models\Nodo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

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
     * funciones básicas para comprobar permisos. Consulta la ACL
     */
    public function leer(?User $user, Nodo $nodo /*, ?Acl $acl = null*/): bool
    {
        $aclist = optional($user)->accessControlList();
        return $this->permisoNodo($user, $nodo, 0b100) ? true : ($aclist ? $nodo->tieneAcceso($aclist, 'leer') : false);
    }

    public function escribir(?User $user, Nodo $nodo /*, ?Acl $acl = null*/): bool
    {
        $aclist = optional($user)->accessControlList();
        return $this->permisoNodo($user, $nodo, 0b010) ? true : ($aclist ? $nodo->tieneAcceso($aclist, 'escribir') : false);
    }

    public function ejecutar(?User $user, Nodo $nodo /*, ?Acl $acl = null*/): bool
    {
        $aclist = optional($user)->accessControlList();
        return $this->permisoNodo($user, $nodo, 0b001) ? true : ($aclist ? $nodo->tieneAcceso($aclist, 'ejecutar') : false);
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
            if (!$nodo)
                return false;

            // elimino el sticky bit
            $permisos = octdec(substr($nodo->permisos, -1));

            if ($user) {
                // Verificar el permiso del propietario (owner)
                if ($nodo->user_id && $nodo->user_id === $user->id && ($permisos & ($bits << 6)) !== 0)
                    return true;

                // Verificar el permiso del grupo
                if ($nodo->group_id && $user->grupos()->where('grupos.id', $nodo->group_id)->exists() && ($permisos & ($bits << 3)) !== 0)
                    return true;
            }
            // Verificar el permiso de otros (others)
            return $permisos & $bits !== 0;

        } catch (\Exception $e) {
            Log::error($e->getMessage(), ['exception' => $e]);
            // La ruta no existe o hay un error al obtener los metadatos
            return false;
        }
    }




}
