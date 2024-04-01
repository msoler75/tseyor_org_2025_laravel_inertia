<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

trait TieneArchivos
{

    public function guardarArchivos($carpeta)
    {
        if (!is_array($this->archivos)||!count($this->archivos))
            return;

        $pathDestino = Storage::disk('public')->path($carpeta);

        $archivosNuevo = [];

        $cambiado = false;
        foreach ($this->archivos as $archivoActual) {
            if (strpos($archivoActual, $carpeta) === FALSE) {
                // hay que copiar el archivo a la nueva ubicación
                if (!Storage::disk('public')->exists($pathDestino)) {
                    Log::info("mkdir public:$pathDestino");
                    Storage::disk('public')->makeDirectory($pathDestino, 0755, true, true);
                }
                $archivoDestino = $carpeta . '/' . basename($archivoActual);
                Storage::disk('public')->move($archivoActual, $archivoDestino);
                $archivosNuevo[] = $archivoDestino;
                $cambiado = true;
            } else {
                $archivosNuevo[] = $archivoActual;
            }
        }

        if ($cambiado)
            $this->update(['archivos' => $archivosNuevo]);
    }
}
