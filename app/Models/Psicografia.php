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
    ];

    public function getCarpetaMedios(bool $formatoRutaRelativa = false): string
    {
        return '/almacen/medios/psicografias';
    }

    // SCOUT

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
