<?php

namespace App\Models;

use App\Traits\EsCategorizable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;

class Psicografia extends ContenidoBaseModel
{
    use CrudTrait;
    use EsCategorizable;
    use Searchable;

    // incluye la categoría 'todas'
    public $incluyeCategoriaTodos = 'Todas';

    protected $fillable = [
        'titulo',
        'slug',
        'categoria',
        'descripcion',
        'imagen',
        // Al estar 'visibilidad' en fillable, ContenidoBaseModel activa los
        // scopes publicado()/publicada()/borrador() automáticamente.
        'visibilidad',
        'para_puzle',
    ];

    protected $casts = [
        'para_puzle' => 'boolean',
    ];

    /**
     * Scope para las psicografías que pueden lanzarse en el puzzle.
     * Es el filtro que consume el endpoint JSON que lee puzle.tseyor.org.
     *
     * @param  Builder  $query
     * @return Builder
     */
    public function scopeParaPuzle($query)
    {
        return $query->where('para_puzle', true);
    }

    public function getCarpetaMedios(bool $formatoRutaRelativa = false): string
    {
        return '/almacen/medios/psicografias';
    }

    // SCOUT

    /**
     * Searchable: solo se indexan las psicografías publicadas, igual que
     * hace Libro con su campo 'visibilidad'.
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
        ];
    }
}
