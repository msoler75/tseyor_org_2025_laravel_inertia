<?php

namespace App\Models;

use App\Models\ContenidoBaseModel;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;

class Psicografia extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    // incluye la categoría 'todas'
    public $incluyeCategoriaTodos = "Todas";

    protected $fillable = [
        'titulo',
        'slug',
        'categoria',
        'descripcion',
        'imagen'
    ];

    public function getMiniaturaAttribute() {
        return '/almacen/'.$this->imagen.'?mw=50&mh=50';
    }

    public function getCarpetaMedios(bool $formatoRutaRelativa= false) : string
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
