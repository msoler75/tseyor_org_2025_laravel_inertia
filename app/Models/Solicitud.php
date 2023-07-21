<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Solicitud extends Model
{

    protected $table="solicitudes";
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'equipo_id',
        'por_user_id', // quien acepta o deniega la solicitud
        'fecha_aceptacion',
        'fecha_denegacion'
    ];

    /**
     * Get the usuario that owns the peticion.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the equipo that owns the peticion.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class);
    }
}
