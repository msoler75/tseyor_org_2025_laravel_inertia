<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;

class Pagina extends SeoModel
{
    use CrudTrait;

    protected $fillable = [
        'titulo',
        'ruta',
        'descripcion',
        'texto',
        'imagen',
        'visibilidad'
    ];

}
