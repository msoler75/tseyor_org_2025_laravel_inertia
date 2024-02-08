<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Busqueda extends Model
{

    protected $fillable = [
        'query',
        'origen',
        'click_url',
        'session_id'
    ];

}
