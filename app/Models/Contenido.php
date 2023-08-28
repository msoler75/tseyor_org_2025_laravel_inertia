<?php

namespace App\Models;

use App\Models\SEOModel;


class Contenido extends SEOModel
{
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
}
