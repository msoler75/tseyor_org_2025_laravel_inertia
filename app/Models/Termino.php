<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;

class Termino extends SEOModel
{
    use CrudTrait;

    protected $table = 'terminos';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'text',
        'visibilidad'
    ];
}
