<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;


class Publicacion extends SEOModel
{
    use CrudTrait;

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

    public static function search($term)
    {
        return static::query()
        ->where('visibilidad', 'P')
        ->where(function($query) use ($term){
           $query->where('titulo', 'LIKE', "%{$term}%")
                ->orWhere('descripcion', 'LIKE', "%{$term}%")
                 ->orWhere('texto', 'LIKE', "%{$term}%");
        });
    }
}
