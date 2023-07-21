<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peticion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'equipo_id',
        'aceptada',
        'fecha_aceptacion',
        'fecha_denegacion'
    ];

    /**
     * Get the usuario that owns the peticion.
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Get the equipo that owns the peticion.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
