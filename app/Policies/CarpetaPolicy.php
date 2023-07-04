<?php

namespace App\Policies;

use App\Models\Permiso;
use App\Models\Carpeta;
use App\Models\User;
// use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Log;

class CarpetaPolicy
{

    /**
     * Determine whether the user can view the model.
     */
    public function leer(?User $user, string $ruta): bool
    {
        Log::info("CarpetaPolicy leer, ruta: $ruta  , user=>" . var_export($user, true));

        $accion = 'leer';

        // Obtener la carpeta correspondiente a la ruta
        $carpeta = Carpeta::where('ruta', $ruta)->first();

        Log::info("carpeta1 = " . var_export($carpeta, true));

        // Si no se encuentra la carpeta específica para esa ruta, se verifica la condición de privada o pública según sus ancestros más cercanos
        if (!$carpeta) {
            $carpeta = Carpeta::whereRaw("'$ruta' LIKE CONCAT(ruta, '%')")
                ->whereNotNull('privada')
                ->orderByRaw('LENGTH(ruta) DESC')
                ->first();

            Log::info("carpeta2 = " . var_export($carpeta, true));
        }


        // si no existe ninguna carpeta o no se tiene información
        if (!$carpeta)
            return false;

        // si es pública, se puede leer
        if (!$carpeta->privada)
            return true;

        // si hemos llegado aquí estamos en una carpeta privada

        // Verificar si hay un permiso heredado desde la carpeta ancestro más cercana
        $permisoHeredado = Permiso::join('carpetas', 'permisos.modelo_id', '=', 'carpetas.id')
            ->where('modelo', 'Carpeta')
            ->whereRaw("'$ruta' LIKE CONCAT(carpetas.ruta, '%')")
            ->where('accion', $accion)
            ->where('permitido', true)
            ->orderByRaw('LENGTH(carpetas.ruta) DESC')
            ->limit(1)
            ->exists();

        if ($permisoHeredado) {
            return true;
        }

        // vamos a buscar una carpeta ancestro que mantenga el rango de 'privada' más cercana y que tenga algun permiso de accion 'leer', para ello, primero obtenemos todas las carpetas ancestro por orden de ruta

        // una vez hecho esto, recorremos el listado y descartamos las carpetas más ancestrales a partir del momento que encontremos una carpeta 'publica', así limpiamos la lista en lo posible

        // con la lista de carpetas, buscamos en la tabla permisos si alguno de los permisos se refiere a esas carpetas, y su valor de 'permitido' es diferente de NULL, priorizando la que tenga más cercanía con $ruta

        // Una vez tenemos ese permiso, sabemos si tenemos el 'permitido' o no

        // si no existe permiso, pues retornamos false

        // Obtener todas las carpetas ancestro por orden de ruta
        $carpetasAncestro = Carpeta::whereRaw("'$ruta' LIKE CONCAT(ruta, '%')")
            ->orderByRaw('LENGTH(ruta) DESC')
            ->get();

        // Recorrer las carpetas ancestro y descartar las carpetas más ancestrales si se encuentra una carpeta 'publica'
        $carpetasFiltradas = collect();
        $publicaEncontrada = false;
        foreach ($carpetasAncestro as $carpetaAncestro) {
            if (!$carpetaAncestro->privada) {
                $publicaEncontrada = true;
            }
            if (!$publicaEncontrada) {
                $carpetasFiltradas->push($carpetaAncestro);
            }
        }

        $equipos_ids = $user ? $user->equipos()->pluck('id') : [];

        // Buscar un permiso en la tabla de permisos para las carpetas filtradas
        $permisoCarpetaAncestro = Permiso::join('carpetas', 'permisos.modelo_id', '=', 'carpetas.id')
            ->where('modelo', 'Carpeta')
            ->whereIn('modelo_id', $carpetasFiltradas->pluck('id'))
            ->whereIn('team_id', $equipos_ids)
            ->where('accion', $accion)
            ->whereNotNull('permitido')
            ->orderByRaw('LENGTH(carpetas.ruta) DESC')
            ->first();

        if ($permisoCarpetaAncestro) {
            return $permisoCarpetaAncestro->permitido;
        }

        return false;
    }



    /**
     * Para checar si se puede hacer una determinada acción en una carpeta para este usuario
     */
    public function puede(string $accion, User $user, string $ruta): bool
    {
        // Obtener todas las carpetas actual y ancestro por orden de ruta
        $carpetasAncestro = Carpeta::whereRaw("'$ruta' LIKE CONCAT(ruta, '%')")
            ->orderByRaw('LENGTH(ruta) DESC')
            ->get();

        $equipos_ids = $user ? $user->equipos()->pluck('id') : [];

        // Buscar un permiso en la tabla de permisos para las carpetas filtradas
        $permisoCarpetaAncestro = Permiso::where('modelo', 'Carpeta')
            ->whereIn('modelo_id', $carpetasAncestro->pluck('id'))
            ->whereIn('team_id', $equipos_ids)
            ->where('accion', $accion)
            ->whereNotNull('permitido')
            ->orderByRaw('LENGTH(ruta) DESC')
            ->first();

        if ($permisoCarpetaAncestro) {
            return $permisoCarpetaAncestro->permitido;
        }

        return false;
    }

    /**
     * Eliminar una carpeta
     */
    public function eliminar(User $user, string $ruta): bool
    {
        return $this->puede('eliminar', $user, $ruta);
    }

    /**
     * Crear nuevas carpetas
     */
    public function crearCarpeta(User $user, string $ruta): bool
    {
        return $this->puede('crearCarpeta', $user, $ruta);
    }

    /**
     * Subir archivos a la carpeta
     */
    public function subirArchivos(User $user, string $ruta): bool
    {
        return $this->puede('subirArchivos', $user, $ruta);
    }
}
