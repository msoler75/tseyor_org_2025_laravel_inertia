<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
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
