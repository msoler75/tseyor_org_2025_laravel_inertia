<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;


class Comunicado extends SEOModel
{
    use CrudTrait;

    protected $fillable = [
        'titulo',
        'numero',
        'categoria',
        'descripcion',
        'texto',
        'imagen',
        'fechaComunicado',
        'visibilidad',
        'slug',
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
