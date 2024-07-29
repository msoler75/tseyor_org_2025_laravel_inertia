<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Pigmalion\StorageItem;

/**
 * No tiene tabla propia, es un wrapper para acceder a los nodos de tipo carpeta
 */
class NodoCarpeta extends Model
{

    protected $table = 'nodos';

    protected $esCarpeta = true;

    protected $fillable = ['ubicacion', 'permisos', 'user_id', 'group_id'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('es_carpeta', function ($query) {
            $query->where('es_carpeta', true);
        });

        static::creating(function ($carpeta) {
            $carpeta->es_carpeta = true;
        });
    }


}
