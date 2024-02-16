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

    protected $casts = [
        'audios' => 'array',
    ];

  

    /**
     * Nombre de los archivos de audio, por defecto
     **/
    public function generarNombreAudio($index)
    {
        $fecha = $this->created_at->format('ymd');
        $audios = $this->audios;
        $multiple = count($audios) > 1;
        return "$fecha ". ($multiple ? " " . ('a' + $index) : "") . ".mp3";
    }

    

}
