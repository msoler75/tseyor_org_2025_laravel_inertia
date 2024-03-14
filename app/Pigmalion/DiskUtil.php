<?php

namespace App\Pigmalion;

class DiskUtil {

    /**
     * En función del path, determina el disco y el path de ese disco
     */
    public static function obtenerDiscoRuta(string $ruta): array
    {
        // si la ruta comienza por "archivos", el disco es "archivos"
        // sino, es "public"
        $ruta = self::normalizarRuta($ruta);
        if ($ruta == '')
            return ['raiz', '']; // raiz


        if (strpos($ruta, 'archivos') === 0) {
            // $ruta = preg_replace("#^archivos\/?#", "", $ruta);
            return ['archivos', $ruta];
        } else if ($ruta == 'mis_archivos') {
            return ['archivos', $ruta];
        } else {
            if(strpos($ruta, 'almacen')===0)
                $ruta = preg_replace("#^almacen\/?#", "", $ruta);
            return ['public', $ruta];
        }
    }


      /**
     * Quita la primera barra si es necesario
     */
    public static function normalizarRuta($ruta)
    {
        if (strpos($ruta, '/') === 0) {
            $ruta = substr($ruta, 1);
        }

        /* if (strpos($ruta, 'almacen') === 0) {
            $ruta = substr($ruta, 8);
        } */
        return $ruta;
    }

}
