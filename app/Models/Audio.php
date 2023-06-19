<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Audio extends Model
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
