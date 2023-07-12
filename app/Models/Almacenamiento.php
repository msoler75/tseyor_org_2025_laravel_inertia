<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Almacenamiento extends Model
{
    protected $table = 'almacenamiento';

    protected $esCarpeta = true;

    protected $fillable = ['ruta', 'permisos', 'user_id', 'group_id'];
}
