<?php

namespace App\Models;

use App\Pigmalion\Markdown;
use App\Traits\EsCategorizable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;

class Normativa extends ContenidoBaseModel
{
    use CrudTrait;
    use EsCategorizable;
    use Searchable;

    // incluye la categoría 'todas'
    public $incluyeCategoriaTodos = 'Todas';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'texto',
        'published_at',
        'visibilidad',
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
            'content' => Markdown::removeMarkdown($this->texto),
        ];
    }
}
