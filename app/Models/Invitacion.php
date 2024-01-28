<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invitacion extends Model
{
    protected $table = 'invitaciones';

    protected $fillable = [
        'equipo_id',
        'user_id',
        'email',
        'token',
        'accepted_at',
        'declined_at',
    ];

    protected $dates = [
        'accepted_at',
        'declined_at',
    ];

    // Relación con el equipo
    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
