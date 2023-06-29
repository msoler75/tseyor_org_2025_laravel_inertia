<?php

namespace App\Models;

use App\Models\SEOModel;


class Comunicado extends SEOModel
{
    protected $fillable = [
        'numero',
        'categoria',
        'titulo',
        'slug',
        'descripcion',
        'texto',
        'imagen',
        'fechaComunicado',
        'visibilidad'
    ];

    // hooks del modelo
    protected static function booted()
    {
        // cuando se guarda el item
        static::saving(function ($comunicado) {
            // $comunicado->slug = "heidi2";
        });
    }

    public static function search($term)
    {
        return static::query()
            ->where('titulo', 'LIKE', "%{$term}%")
            ->orWhere('texto', 'LIKE', "%{$term}%")
            ->orWhere('numero', '=', "{$term}");
    }
}
