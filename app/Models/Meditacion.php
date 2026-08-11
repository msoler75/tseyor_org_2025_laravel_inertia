<?php

namespace App\Models;

use App\Pigmalion\Markdown;
use App\Traits\EsCategorizable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;

class Meditacion extends ContenidoBaseModel
{
    use CrudTrait;
    use EsCategorizable;
    use Searchable;

    // incluye la categoría 'todas'
    public $incluyeCategoriaTodos = 'Todas';

    protected $table = 'meditaciones';

    protected $fillable = [
        'titulo',
        'slug',
        'categoria',
        'descripcion',
        'texto',
        'audios',
        'visibilidad',
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
