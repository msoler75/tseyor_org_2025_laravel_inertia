<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = [
        'uid',
        'texto',
        'user_id',
        'respuesta_a',
        'eliminado'
    ];
}
