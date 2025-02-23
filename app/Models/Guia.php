<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;

class Guia extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

    protected $fillable = [
        'nombre',
        'slug',
        'categoria',
        'descripcion',
        'texto',
        'imagen',
        'bibliografia',
        'libros',
        'visibilidad'
    ];


    // obtiene el texto para el buscador, lo que nos interesa que encuentre de este contenido
    public function getTextoContenidoBuscador()
    {
        // incluimos la descripción breve en la búsqueda
        return $this->descripcion;
    }

    /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }

}
