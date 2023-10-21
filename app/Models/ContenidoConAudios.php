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
     * Nombre de los archivos de audio, por defecto
     **/
    public function generarNombreAudio($index)
    {
        $fecha = date('ymd', strtotime($this->created_at));
        $audios = gettype($this->audios) === "string" ? json_decode($this->audios, true) : $this->audios;
        $multiple = count($audios) > 1;
        return "$fecha ". ($multiple ? " " . ('a' + $index) : "") . ".mp3";
    }

    /**
     * En qué carpeta se guardarán los audios
     **/
    public function generarRutaAudios()
    {
        $modelName = $this->getMorphClass();
        $año = date('Y', strtotime($this->created_at));
        return "media/$modelName/$año";
    }

}
