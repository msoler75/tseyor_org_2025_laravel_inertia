<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nodo extends Model
{
    protected $fillable = ['ruta', 'permisos', 'user_id', 'group_id', 'es_carpeta'];


    protected $attributes = [
        'ruta' => 'archivos',
        'permisos' => '1755',
        'user_id' => null, // Por ejemplo, puedes usar 0 como valor predeterminado para el user_id
        'group_id' => 1, // O null si no tiene grupo por defecto
        'es_carpeta' => 1, // Por ejemplo, un valor booleano como valor predeterminado
    ];
}
