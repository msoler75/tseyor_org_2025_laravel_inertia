<?php

namespace App\Models;

use App\Pigmalion\Markdown;
use App\Traits\EsCategorizable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;

class Publicacion extends ContenidoBaseModel
{
    use CrudTrait;
    use EsCategorizable;
    use Searchable;

    protected $table = 'publicaciones';

    protected $fillable = [
        'titulo',
        'slug',
        'categoria',
        'descripcion',
        'texto',
        'imagen',
        'published_at',
        'visibilidad',
        'user_id',
        'equipo_id',
    ];

    protected $dates = [
        'published_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id', 'id');
    }

    // accesors
    public function getNombreUsuarioAttribute()
    {
        return $this->user->name; // Reemplaza `name` por el nombre del atributo que contiene el nombre del usuario en tu modelo `User`
    }

    public function getNombreEquipoAttribute()
    {
        return $this->equipo->nombre; // Reemplaza `nombre` por el nombre del atributo que contiene el nombre del grupo en tu modelo `Grupo`
    }

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
