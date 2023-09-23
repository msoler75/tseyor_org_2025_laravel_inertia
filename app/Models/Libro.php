<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;

class Libro extends ContenidoBaseModel
{

    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'edicion',
        'paginas',
        'pdf',
        'visibilidad'
    ];

    public $table = 'libros';



    /**
     * Searchable: Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion
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
     * ContenidoBaseModel: obtiene el texto para el buscador global
     */
    /* public function getTextoContenidoBuscador()
    {
        // incluimos la descripcion breve
        return $this->descripcion;
    }*/


}
