<?php

namespace App\Models;

use App\Models\SEOModel;
use Laravel\Scout\Searchable;

class Contenido extends SEOModel
{

    use Searchable;


    protected $fillable = [
        'coleccion',
        'id_ref',
        'slug_ref',
        'titulo',
        'descripcion',
        'texto',
        'imagen',
        'fecha',
        'visibilidad'
    ];

    protected $dates = [
        'fecha',
    ];

    /*
    public static function search($term)
    {
        return static::select(['coleccion', 'id_ref', 'descripcion', 'imagen', 'slug_ref', 'fecha'])
            ->where('titulo', 'LIKE', "%{$term}%")
            // ->orWhereRaw('MATCH (descripcion) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
            // ->orWhereRaw('MATCH (texto) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
            ->orWhere('descripcion', 'LIKE', "%{$term}%")
            ->orWhere('texto', 'LIKE', "%{$term}%")
            ->get();
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
        return $this->toArray();
    }
}
