<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Evento extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'texto',
        'imagen',
        'published_at',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'visibilidad'
    ];

    protected $dates = [
        'published_at',
        'fecha_inicio',
        'fecha_fin',
    ];
}
