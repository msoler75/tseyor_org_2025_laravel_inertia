<?php

namespace App\Models;

class Preguntas extends ContenidoBaseModel
{

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'texto'
    ];

}
