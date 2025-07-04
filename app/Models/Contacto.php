<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use App\Pigmalion\Countries;
use App\Http\Controllers\ContactosController;
use Laravel\Scout\Searchable;

class Contacto extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

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
        'visibilidad'
    ];


    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Solo ejecutar en producción
            if (app()->environment('production')) {
                ContactosController::rellenarLatitudYLongitud($model);
            }
        });

    }

     // ACCESOR
     public function getNombrePaisAttribute()
     {
         return Countries::getCountry($this->pais);
     }


     // obtiene el texto para el buscador, lo que nos interesa que encuentre de este contenido
     public function getTextoContenidoBuscador() {
        return $this->poblacion .", ". $this->NombrePais;
     }


     public function centro() // centro relacionado con este contacto (si lo hay)
     {
         return $this->hasOne(Centro::class, 'contacto_id');
     }

     public function usuario() // usuario relacionado con este contacto (si lo hay)
     {
         return $this->hasOne(User::class, 'contacto_id');
     }
}
