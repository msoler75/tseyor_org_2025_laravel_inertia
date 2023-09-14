<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;

class Pagina extends ContenidoBaseModel
{
    use CrudTrait;

    protected $table = 'paginas';

    protected $fillable = [
        'titulo',
        'ruta',
        'descripcion',
        'texto',
        'imagen',
        'visibilidad'
    ];



     /**
     * Función heredable para cada modelo
     */
    public function getTextoContenidoBuscador() {
        // incluimos la descripcion breve (SEO) y el texto de la página
        return $this->descripcion . " " . html_entity_decode(strip_tags($this->texto));
    }

}
