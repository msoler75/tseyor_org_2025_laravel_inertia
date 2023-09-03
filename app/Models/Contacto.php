<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;
use App\Pigmalion\Countries;
use App\Http\Controllers\ContactosController;

class Contacto extends SEOModel
{
    use CrudTrait;

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


    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Acciones antes de guardar el modelo
            ContactosController::rellenarLatitudYLongitud($model);
        });

    }


     // ACCESOR
     public function getNombrePaisAttribute()
     {
         return Countries::getCountry($this->pais);
     }

}
