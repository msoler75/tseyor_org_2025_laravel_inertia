<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Almacenamiento;
use App\Models\User;
use Illuminate\Support\Facades\Log;


/**
 * El almacenamiento simula los permisos de linux con usuarios y grupos
 * Este clase sirve para gestionar los permisos de acceso a un elemento de almacenamiento, que puede ser un archivo o una carpeta
 */
class AlmacenamientoPolicy
{
    /**
     * La umask establece los permisos que se deben restar de los permisos predeterminados totales del sistema de archivos
     * para determinar los permisos reales que se aplicarán al nuevo archivo o carpeta.
     * La umask es una configuración en el sistema operativo Unix y Linux que controla los permisos predeterminados
     * que se aplican a los archivos y carpetas recién creados.
     *  */
    public $umask = "022";


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
    public function obtenerItem($ruta)
    {
        //$carpeta = Carpeta::where('ruta', $ruta)->first();
        $carpeta = Almacenamiento::select(['almacenamiento.*', 'grupos.slug as propietario_grupo', 'users.slug as propietario_usuario'])
            ->leftJoin('users', 'users.id', '=', 'user_id')
            ->leftJoin('grupos', 'grupos.id', '=', 'group_id')
            ->whereRaw("'$ruta' LIKE CONCAT(ruta, '%')")
            ->orderByRaw('LENGTH(ruta) DESC')
            ->first();

        return $carpeta;
    }

    private function permisoAdicional(?User $user, Almacenamiento $item, string $accion): bool
    {
        return false;

        $user_id = $user ? $user->id : -1;
        $grupos_ids = $user ? $user->grupos()->pluck('id') : [];

        $permiso = Permiso::join('almacenamiento', 'permisos.modelo_id', '=', 'almacenamiento.id')
            ->where('modelo', 'Carpeta')
            ->whereIn('modelo_id', $item->pluck('id'))
            ->whereRaw("'$item->ruta' LIKE CONCAT(almacenamiento.ruta, '%')")
            ->where(function ($query) use ($grupos_ids, $user_id) {
                $query->whereIn('permisos.group_id', $grupos_ids)
                    ->orWhere('permisos.user_id', $user_id);
            })
            ->where('accion', $accion)
            ->orderByRaw('LENGTH(almacenamiento.ruta) DESC')
            ->limit(1)
            ->exists();

        return $permiso;
    }

    private function puede(?User $user, string $ruta, string $accion, int $bits): bool
    {
        $item = $this->obtenerItem($ruta);

        if (!$item) {
            return false;
        }

        $permisos = intval($item->permisos ?? 0, 8);

        // Verificar el permiso del propietario (owner)
        if ($user && $item->user_id && $item->user_id === optional($user)->id && ($permisos & ($bits << 6)) !== 0) {
            return true;
        }

        // Verificar el permiso del grupo
        if ($user && $item->group_id && $user->grupos()->where('id', $item->group_id)->exists() && ($permisos & ($bits << 3)) !== 0) {
            return true;
        }

        // Verificar el permiso de otros (others)
        if (($permisos & $bits) !== 0) {
            return true;
        }

        return $this->permisoAdicional($user, $item, $accion);
    }
}
