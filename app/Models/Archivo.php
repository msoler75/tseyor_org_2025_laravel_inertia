<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $table = 'nodos';

    protected $esCarpeta = false;

    protected $fillable = ['ruta', 'permisos', 'user_id', 'group_id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('es_carpeta', function ($query) {
            $query->where('es_carpeta', false);
        });


        static::creating(function ($carpeta) {
            $carpeta->es_carpeta = false;
        });
    }

}
