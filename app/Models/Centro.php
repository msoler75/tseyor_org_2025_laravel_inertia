<?php

namespace App\Models;

use App\Pigmalion\Countries;
use App\Traits\TieneImagen;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Centro extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use SoftDeletes;
    use TieneImagen;

    protected $fillable = [
        'nombre',
        'slug',
        'imagen',
        'descripcion',
        'entradas',
        'libros',
        'poblacion',
        'pais',
        'contacto_id',
    ];

    public function contacto() // contacto relacionado con este centro
    {
        return $this->belongsTo(Contacto::class, 'contacto_id')
            ->publicado();
    }

    // ACCESOR
    public function getNombrePaisAttribute()
    {
        return Countries::getCountry($this->pais);
    }
}
