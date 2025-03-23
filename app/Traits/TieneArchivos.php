<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\StorageItem;

trait TieneArchivos
{

    public function guardarArchivos($carpeta)
    {
        if (!is_array($this->archivos) || !count($this->archivos))
            return false;

        StorageItem::ensureDirExists($carpeta);

        $sti = new StorageItem($carpeta);
        $disk = $sti->disk;
        $dest = $sti->relativeLocation;

        if ($disk != 'public') {
            Log::error("TieneArchivos::guardarArchivos ($carpeta): No se puede guardar en un disco distinto de public");
            return;
        }

        $archivosNuevo = [];
        $cambiado = false;
        foreach ($this->archivos as $archivoActual) {
            $pathFileTarget = $dest . '/' . basename($archivoActual);
            if (strpos($archivoActual, $carpeta) === FALSE) {
                // hay que copiar el archivo a la nueva ubicación
                Storage::disk('public')->move($archivoActual, $pathFileTarget);
                $archivosNuevo[] = $pathFileTarget;
                $cambiado = true;
            } else {
                $archivosNuevo[] = $archivoActual;
            }
        }


        if ($cambiado)
        {
            $this->archivos=$archivosNuevo;
            // $this->saveQuietly();
        }

        return $cambiado;
    }
}
