<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nodo extends Model
{
    protected $fillable = ['ruta', 'permisos', 'user_id', 'group_id', 'es_carpeta'];
}
