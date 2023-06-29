<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadioItem extends Model
{
    protected $table = "radio";

    protected $fillable = [
        'audio',
        'duracion',
        'categoria',
        'desactivado'
    ];
}
