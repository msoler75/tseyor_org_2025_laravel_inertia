<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;


class Audio extends SEOModel
{
    use CrudTrait;
    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'audios';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'audio',
        'visibilidad',
        'duracion'
    ];
}
