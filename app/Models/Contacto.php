<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Contacto extends Model
{
    protected $fillable = [
        'nombre',
        'slug',
        'imagen',
        'pais',
        'poblacion',
        'provincia',
        'direccion',
        'codigo',
        'telefono',
        'social',
        'email',
        'latitud',
        'longitud',
        'centro_id',
        'user_id',
        'visibilidad'
    ];
}
