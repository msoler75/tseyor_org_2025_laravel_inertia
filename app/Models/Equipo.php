<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\SEOModel;


class Equipo extends SEOModel
{
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'categoria',
        'user_id',
        'group_id'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'equipo_user')
            ->using(Membresia::class)
            ->withPivot(['user_id', 'rol'])
            ->withTimestamps();
    }
}

class Membresia extends Pivot
{
    protected $table = 'equipo_user';

    protected $casts = [
        'user_id' => 'integer',
        'equipo_id' => 'integer',
    ];
}
