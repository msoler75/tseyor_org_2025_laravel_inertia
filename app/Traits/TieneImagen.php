<?php

namespace App\Traits;

trait TieneImagen
{
    // accesor attribute
    public function getImagenUrlAttribute()
    {
        $src = $this->imagen;
        if (!$src)
            return null;
        // si empieza por /almacen o por http: ya devolvemos
        if (strpos($src, 'almacen/') === 0 || strpos($src, '/almacen/') === 0 || strpos($src, 'http:') === 0 || strpos($src, 'https:') === 0)
            return $src;

        // si no, es una ruta relativa al almacen
        $ruta = $src;
        if (preg_match("/^\/?archivos/", $ruta)) {
            // $ruta = preg_replace("#^archivos\/?#", "", $ruta);
            return $src;
        } else {

        }

        // si no, devolvemos la ruta completa
        return url("/almacen/".$src);
    }
}
