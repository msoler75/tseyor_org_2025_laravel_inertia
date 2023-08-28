<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;
use Laravel\Scout\Searchable;


class Publicacion extends SEOModel
{
    use CrudTrait;
    use Searchable;

    protected $table = 'publicaciones';

    protected $fillable = [
        'titulo',
        'descripcion',
        'texto',
        'imagen',
        'published_at',
        'visibilidad'
    ];

    protected $dates = [
        'published_at',
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
            'texto' => $this->texto,
            'categoria' => $this->categoria,
            'updated_at' => $this->updated_at,
        ];
    }
}
