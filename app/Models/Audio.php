<?php

namespace App\Models;

use App\Models\SEOModel;


class Audio extends SEOModel
{
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
