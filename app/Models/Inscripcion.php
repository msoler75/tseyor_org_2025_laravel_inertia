<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\SEOModel;

class Inscripcion extends SEOModel
{
    use CrudTrait;
    use HasFactory;

    protected $table = 'inscripciones';

    protected $fillable = [
        'nombre',
        'fecha_nacimiento',
        'ciudad',
        'region',
        'pais',
        'email',
        'telefono',
        'comentario',
    ];
}
