<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Nodo;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Cache;


/**
 * Esta clase simula los permisos de linux con usuarios y grupos.
 * Un nodo es un archivo o una carpeta.
 * Podemos comprobar los permisos básicos (leer, escribir, ejecutar, o rwx).
 * Además de los permisos básicos, está el ACL (Access Control List) con el modelo Permiso.
 */
class LinuxPolicy
{

    /**
     * funciones básicas para comprobar permisos. Consulta la ACL
     */
    public static function leer(Nodo $nodo, ?User $user, $acl = null): bool
    {
        return self::permisoNodo($nodo, $user, 0b100) ? true : ($acl ? self::tieneAcceso($nodo, $acl, 'leer') : false);
    }

    public static function escribir(Nodo $nodo, ?User $user, $acl = null): bool
    {
        return self::permisoNodo($nodo, $user, 0b010) ? true : ($acl ? self::tieneAcceso($nodo, $acl, 'escribir') : false);
    }

    public static function ejecutar(Nodo $nodo, ?User $user, $acl = null): bool
    {
        return self::permisoNodo($nodo, $user, 0b001) ? true : ($acl ? self::tieneAcceso($nodo, $acl, 'ejecutar') : false);
    }

    /**
     * Obtiene el nodo más cercano siendo el mismo o antecesor de la ruta
     */
    public static function nodoHeredado($ruta)
    {
        $nodo = Nodo::select(['nodos.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->whereRaw("'$ruta' LIKE CONCAT(nodos.ruta, '%')")
            ->orderByRaw('LENGTH(nodos.ruta) DESC')
            ->first();

        if (!$nodo) {
            // crea un nodo por con los permisos por defecto
            $nodo = new Nodo();
            $nodo->ruta =  ltrim($ruta, '/');
        }
        return $nodo;
    }

    /**
     * Obtiene todos los nodos de la carpeta, sin incluir el nodo de la carpeta
     */
    public static function nodosCarpeta($ruta)
    {
        return Nodo::select(['nodos.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->where('nodos.ruta', 'LIKE', $ruta . '/%')
            ->whereRaw("LENGTH(nodos.ruta) - LENGTH(REPLACE(nodos.ruta, '/', '')) = " . (substr_count($ruta, '/') + 1))
            ->orderByRaw('LENGTH(nodos.ruta) ASC')
            ->get();
    }

    /**
     * Usa la máscara de bits para saber si hay permisos para este nodo
     */
    public static function permisoNodo(Nodo $nodo, ?User $user, int $bits): bool
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
     * Access Control List. Devuelve un listado de permisos para este usuario
     * $verbos es opcional, permite preseleccionar los permisos segun ciertos verbos específicos, se usa para mejorar rendimiento
     */
    public static function acl(?User $user, array $verbos = null)
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
            ->where(function ($query) use ($verbos) {
                if ($verbos)
                    foreach ($verbos as $verbo) {
                        $query->orWhere('verbos', 'LIKE', '%' . $verbo . '%');
                    }
            })
            ->get();
    }


    /**
     * Comprueba con la ACL si tiene el acceso a un nodo en concreto
     */
    public static function tieneAcceso(Nodo $nodo, $acl, $verbo = null)
    {
        // filtramos por verbo
        if ($verbo)
            $acl = $acl->filter(function ($nodo) use ($verbo) {
                return strpos($nodo->verbos, $verbo) !== false;
            });

        // tiene acceso global para todos los nodos?
        if ($acl->whereNull('modelo_id')->count() > 0)
            return true;

        // tiene acceso a este nodo?
        if ($acl->where('modelo_id', $nodo->id)->count() > 0)
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

    /**
     * Renombra los nodos afectados por cambios en la ruta
     * Ejemplo: renombrar un archivo, o mover un archivo de una carpeta a otra
     */
    public static function move($from, $to)
    {
        Nodo::where('ruta', 'like', "$from/%")
            ->orWhere('ruta', $from)
            ->update([
                'ruta' => DB::raw("CONCAT('$to', SUBSTRING(ruta, LENGTH('$from') + 1))")
            ]);
    }


    public static function crearNodo(?User $user, string $ruta, bool $es_carpeta = false)
    {
        // Obtén el valor de umask desde el archivo de configuración
        $umask = Config::get('app.umask');

        // Convertir umask y permisos a octal
        $umask = octdec($umask);

        // aplicamos el umask, con el sticky bit 1
        $permisos = 01777 & ~$umask;

        Nodo::create([
            'ruta' => $ruta,
            'user_id' => optional($user)->id ?? 1,
            'group_id' => 1,
            'permisos' => decoct($permisos),
            'es_carpeta' => $es_carpeta
        ]);
    }
}
