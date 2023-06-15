<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Centro extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'imagen',
        'contacto_id',
        'pais',
        'poblacion'
    ];

}
