<?php

namespace App\Pigmalion;

use Illuminate\Support\Facades\Storage;


class DiskUtil {

    /**
     * En función del path, determina el disco y el path de ese disco
     */
    public static function obtenerDiscoRuta(string $ruta): array
    {
        // dd($ruta);
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

    public static function getRealPath($rutaOrig) {
        return realpath(self::getPath($rutaOrig));
    }

    public static function getPath($rutaOrig) {
        list($disk, $ruta) = self::obtenerDiscoRuta($rutaOrig);
        return Storage::disk($disk)->path($ruta);
    }

    public static function getRutaRelativa($rutaOrig) {
        list($disk, $ruta) = self::obtenerDiscoRuta($rutaOrig);
        return $ruta;
    }

    /**
     * $dir A veces es una ruta absoluta, a veces una ruta de '/almacen/...
     */
    public static function ensureDirExists($dir) {
        $home = base_path();
        if(strpos($dir, $home)===0) {
            // es una ruta física
            if(!file_exists($dir))
                mkdir($dir, 0777, true);
            return;
        }

        // es una ruta tipo '/almacen'
        list($disk, $ruta) = self::obtenerDiscoRuta($dir);
        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk($disk)->exists($ruta)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk($disk)->makeDirectory($ruta);
        }
    }

    public static function normalizePath($ruta) {
        if (!$ruta) return $ruta;
        if (preg_match("/^https?:\/\//", $ruta)) return $ruta;
        // list($disk, $ruta) = self::obtenerDiscoRuta($ruta);
        if(preg_match("/^\/archivos\//", $ruta)) return $ruta;
        if(preg_match("/^\/storage\//", $ruta)) return preg_replace("#^\/storage\/?#", "/almacen/", $ruta);
        if(preg_match("/^\/almacen\//", $ruta)) return $ruta;
        return "/almacen/$ruta";
    }

}
