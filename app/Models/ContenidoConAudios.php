<?php

namespace App\Models;

/*
 Clase base para poder procesar en una job los audios con el AudioConverter. La Job es App\Jobs\ProcesarAudios
 */

class ContenidoConAudios extends ContenidoBaseModel
{

    protected $fillable = [
        'audios'
    ];


    /**
     * Obtiene un array con strings que indican los archivos de audio
     */
    public function obtenerAudiosArray() {
        if(gettype($this->audios) === "string") {
            // primero probamos si es un json
            $r = json_decode($this->audios, true);
            if($r) return [$r];
            // es una ruta
        }
        return $this->audios;
    }

    /**
     * Nombre de los archivos de audio, por defecto
     **/
    public function generarNombreAudio($index)
    {
        $fecha = $this->created_at->format('ymd');
        $audios = $this->obtenerAudiosArray();
        dd($audios);
        $multiple = count($audios) > 1;
        return "$fecha ". ($multiple ? " " . ('a' + $index) : "") . ".mp3";
    }

    /**
     * En qué carpeta se guardarán los audios
     **/
    public function generarRutaAudios()
{
        $modelName = $this->getMorphClass();
        $año = $this->created_at->year;
        return "medios/$modelName/$año";
    }

}
