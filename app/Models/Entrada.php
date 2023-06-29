<?php

namespace App\Models;

use App\Models\SEOModel;


class Entrada extends SEOModel
{
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'texto',
        'imagen',
        'published_at',
        'visibilidad'
    ];

    protected $dates = [
        'published_at'
    ];
}
