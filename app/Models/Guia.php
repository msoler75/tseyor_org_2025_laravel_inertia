<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guia extends Model
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
