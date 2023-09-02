<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;
use Laravel\Scout\Searchable;

class Libro extends SEOModel
{

    use CrudTrait;
    use Searchable;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'edicion',
        'paginas',
        'pdf',
        'visibilidad'
    ];

    /* public static function search($term)
    {
        return static::query()
        ->where('visibilidad', 'P')
        ->where(function($query) use ($term){
           $query->where('titulo', 'LIKE', "%{$term}%")
                 ->orWhere('descripcion', 'LIKE', "%{$term}%");
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
}
