<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;


class Centro extends SEOModel
{
    use CrudTrait;

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

}
