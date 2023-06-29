<?php

namespace App\Models;

use App\Models\SEOModel;


class Centro extends SEOModel
{
    protected $fillable = [
        'nombre',
        'slug',
        'imagen',
        'contacto_id',
        'pais',
        'poblacion'
    ];

}
