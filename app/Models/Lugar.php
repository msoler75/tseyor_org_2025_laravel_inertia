<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SEOModel;

class Lugar extends SEOModel
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'lugares';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'texto',
        'libros',
        'relacionados'
    ];
}
