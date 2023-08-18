<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'url',
        'texto',
        'user_id',
        'respuesta_a',
        'eliminado'
    ];
}
