<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use App\Pigmalion\Countries;
use App\Traits\TieneImagen;
use Laravel\Scout\Searchable;

class Centro extends ContenidoBaseModel
{
    use CrudTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use TieneImagen;
    use Searchable;

    protected $fillable = [
        'nombre',
        'slug',
        'imagen',
        'descripcion',
        'entradas',
        'libros',
        'poblacion',
        'pais',
        'contacto_id'
    ];



    public function contacto() // contacto relacionado con este centro
    {
        return $this->belongsTo(Contacto::class, 'contacto_id')
            ->where('visibilidad', 'P');
    }


    // ACCESOR
    public function getNombrePaisAttribute()
    {
        return Countries::getCountry($this->pais);
    }



}
