<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Entrada extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'texto',
        'imagen',
        'estado',
        'published_at'
    ];
}
