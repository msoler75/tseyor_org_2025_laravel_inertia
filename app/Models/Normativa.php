<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;

class Normativa extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    // incluye la categoría 'todas'
    public $incluyeCategoriaTodos = "Todas";

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'texto',
        'published_at',
        'visibilidad'
    ];

    protected $dates = [
        'published_at',
    ];


    // SCOUT


     /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }


   /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id, // <- Always include the primary key
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'content' => $this->texto
        ];
    }
}
