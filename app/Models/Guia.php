<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;

class Guia extends SEOModel
{
    use CrudTrait;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'categoria',
        'texto', //presentación
        'comunicado',
        'experiencia',
        'imagen',
        'citas',
        'libros',
        'relacionados'
    ];
}
