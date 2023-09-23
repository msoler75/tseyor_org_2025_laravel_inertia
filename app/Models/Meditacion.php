<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;


class Meditacion extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

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


    /* public static function search($term)
    {
        return static::query()
        ->where('visibilidad', 'P')
        ->where(function($query) use ($term){
           $query->where('titulo', 'LIKE', "%{$term}%")
                ->orWhere('descripcion', 'LIKE', "%{$term}%")
                 ->orWhere('texto', 'LIKE', "%{$term}%");
        });
    }
    */



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
