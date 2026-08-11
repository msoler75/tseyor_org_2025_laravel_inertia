<?php

namespace App\Models;

use App\Pigmalion\Markdown;
use App\Traits\TieneImagen;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;

class Noticia extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use TieneImagen;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'texto',
        'imagen',
        'published_at',
        'visibilidad',
    ];

    protected $dates = [
        'published_at',
    ];

    /* public static function search($term)
    {
        return static::query()
            ->publicada()
            ->where(function ($query) use ($term) {
                $query->where('titulo', 'LIKE', "%{$term}%")
                    ->orWhere('descripcion', 'LIKE', "%{$term}%")
                    ->orWhere('texto', 'LIKE', "%{$term}%");
            });
    }
    */

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
