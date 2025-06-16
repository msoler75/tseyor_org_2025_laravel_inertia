<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;

class Pagina extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

    protected $table = 'paginas';

    protected $fillable = [
        'titulo',
        'ruta',
        'atras_ruta',
        'atras_texto',
        'descripcion',
        'texto',
        'palabras_clave',
        'visibilidad'
    ];


    /**
     * Función heredable para cada modelo
     */
    public function getTextoContenidoBuscador()
    {
        // incluimos la descripcion breve (SEO) y las palabras clave
        return rtrim($this->descripcion, "\t\n .") . ". " . $this->palabras_clave;
        //html_entity_decode(strip_tags($this->palabras_clave));
    }
}
