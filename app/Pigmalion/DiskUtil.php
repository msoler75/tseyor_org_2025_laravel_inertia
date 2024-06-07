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
        list($disk, $ruta) = self::obtenerDiscoRuta($rutaOrig);
        return realpath(Storage::disk($disk)->path($ruta));
    }

    public static function ensureDirExists($dir) {
        // removemos la parte de ruta de la app de $dir
        /*$home = base_path();
        $rutaOrig = str_replace($home, '', $dir);
        $rutaOrig = str_replace("/storage/app/public/", "/almacen/", $rutaOrig);
        list($disk, $ruta) = self::obtenerDiscoRuta($rutaOrig);
        // sdd($dir, $rutaOrig, $disk, $ruta);
        // Verificar si la carpeta existe en el disco 'public'
        if (!Storage::disk($disk)->exists($ruta)) {
            // Crear la carpeta en el disco 'public'
            Storage::disk($disk)->makeDirectory($ruta);
        }*/
        if(!file_exists($dir))
            mkdir($dir, 0777, true);
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
