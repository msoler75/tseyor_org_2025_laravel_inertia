<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Equipo extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'equipo_user')
            ->using(Membresia::class)
            ->withPivot(['user_id', 'rol']);
    }
}

class Membresia extends \Illuminate\Database\Eloquent\Relations\Pivot
{
    protected $table = 'equipo_user';

    protected $casts = [
        'user_id' => 'integer',
        'equipo_id' => 'integer',
    ];
}
