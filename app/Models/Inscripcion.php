<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inscripcion extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'day',
        'month',
        'year',
        'city',
        'region',
        'email',
        'phone',
        'contact',
        'agreement',
    ];
}
