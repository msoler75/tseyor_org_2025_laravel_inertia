<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use App\Pigmalion\Countries;
use App\Traits\TieneImagen;

class Centro extends ContenidoBaseModel
{
    use CrudTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use TieneImagen;

    protected $fillable = [
        'nombre',
        'slug',
        'imagen',
        'pais',
        'poblacion'
    ];



    public function contacto() // contacto relacionado con este centro
    {
        return $this->belongsTo(Contacto::class, 'contacto_id');
    }


    // ACCESOR
    public function getNombrePaisAttribute()
    {
        return Countries::getCountry($this->pais);
    }



}
