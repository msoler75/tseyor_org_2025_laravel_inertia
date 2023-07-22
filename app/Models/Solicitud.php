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
        'user_id', // usuario que realiza la solicitud
        'equipo_id', // equipo donde realiza la solicitud de ingreso
        'por_user_id', // usuario que acepta o deniega la solicitud
        'fecha_aceptacion',
        'fecha_denegacion'
    ];

    /**
     * Get the usuario that owns the peticion.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the equipo that owns the peticion.
     */
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id', 'id');
    }
}
