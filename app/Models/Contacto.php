<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use App\Pigmalion\Countries;
use App\Http\Controllers\ContactosController;

class Contacto extends ContenidoBaseModel
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


     // obtiene el texto para el buscador, lo que nos interesa que encuentre de este contenido
     public function getTextoContenidoBuscador() {
        return $this->poblacion .", ". $this->NombrePais;
     }

}
