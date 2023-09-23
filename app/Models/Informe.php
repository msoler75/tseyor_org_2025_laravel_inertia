<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;


class Informe extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    protected $table = 'informes';

    protected $fillable = [
        'titulo',
        'categoria',
        'equipo_id',
        'descripcion',
        'texto',
        'audios',
        'visibilidad',
    ];


      public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id', 'id');
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
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'texto' => $this->texto,
        ];
    }
}
