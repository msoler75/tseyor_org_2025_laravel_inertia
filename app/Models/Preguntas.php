<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preguntas extends Model
{

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'texto'
    ];

}
