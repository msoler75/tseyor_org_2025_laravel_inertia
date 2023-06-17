<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Libro extends Model
{
    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'estado',
        'edicion',
        'paginas',
        'pdf',
        'published_at'
    ];

    protected $dates = [
        'published_at',
    ];

    public static function search($term)
    {
        return static::query()
        ->where('estado', 'P')
        ->where(function($query) use ($term){
           $query->where('titulo', 'LIKE', "%{$term}%")
                 ->orWhere('descripcion', 'LIKE', "%{$term}%");
        });
    }
}
