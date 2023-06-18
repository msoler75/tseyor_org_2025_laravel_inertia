<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Noticia extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
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
