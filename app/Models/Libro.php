<?php

namespace App\Models;

use App\Models\SEOModel;


class Libro extends SEOModel
{
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'edicion',
        'paginas',
        'pdf',
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
                 ->orWhere('descripcion', 'LIKE', "%{$term}%");
        });
    }
}
