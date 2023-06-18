<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Comunicado extends Model
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
