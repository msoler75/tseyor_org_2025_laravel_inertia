<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;

class Entrada extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

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
