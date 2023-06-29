<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SEOModel;

class Guia extends SEOModel
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'categoria',
        'texto',
        'experiencia',
        'imagen',
        'citas',
        'libros',
        'relacionados'
    ];
}
