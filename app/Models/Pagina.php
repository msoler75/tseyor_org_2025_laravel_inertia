<?php

namespace App\Models;

use App\Models\SEOModel;

class Pagina extends SeoModel
{

    protected $fillable = [
        'titulo',
        'url',
        'descripcion',
        'texto',
        'imagen',
        'visibilidad'
    ];

}
