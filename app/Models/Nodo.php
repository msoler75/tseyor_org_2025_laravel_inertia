<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class Nodo extends Model
{
    protected $fillable = ['ruta', 'permisos', 'user_id', 'group_id', 'es_carpeta'];


    protected $attributes = [
        'ruta' => 'archivos',
        'permisos' => '1755',
        'user_id' => null, // Por ejemplo, puedes usar 0 como valor predeterminado para el user_id
        'group_id' => 1, // O null si no tiene grupo por defecto
        'es_carpeta' => 1, // Por ejemplo, un valor booleano como valor predeterminado
    ];



    /**
     * Obtiene el nodo más cercano siendo el mismo o antecesor de la ruta
     */
    public static function desde($ruta)
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
    public static function hijos($ruta)
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
     * Renombra los nodos afectados por cambios en la ruta
     * Ejemplo: renombrar un archivo, o mover un archivo de una carpeta a otra
     */
    public static function mover($from, $to)
    {
        Nodo::where('ruta', 'like', "$from/%")
            ->orWhere('ruta', $from)
            ->update([
                'ruta' => DB::raw("CONCAT('$to', SUBSTRING(ruta, LENGTH('$from') + 1))")
            ]);
    }


    public static function crear(?User $user, string $ruta, bool $es_carpeta = false)
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
