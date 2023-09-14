<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;

class Guia extends ContenidoBaseModel
{
    use CrudTrait;

    protected $fillable = [
        'nombre',
        'slug',
        'categoria',
        'descripcion',
        'texto',
        'imagen',
        'libros'
    ];



      // obtiene el texto para el buscador, lo que nos interesa que encuentre de este contenido
      public function getTextoContenidoBuscador() {
        // incluimos la descripción breve en la búsqueda
        return $this->descripcion;
     }
}
