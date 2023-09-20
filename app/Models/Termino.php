<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;

class Termino extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

    protected $table = 'terminos';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'texto',
        'visibilidad'
    ];



    /**
     * Searchable: Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'texto' => $this->texto
        ];
    }

    /**
     * Searchable: Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }


    /**
     * Función heredable de ContenidoBaseModel para la busqueda global
     */
    public function getTextoContenidoBuscador()
    {
        // return html_entity_decode(strip_tags($this->texto));
        return "";
    }
}
