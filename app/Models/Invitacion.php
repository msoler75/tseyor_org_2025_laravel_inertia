<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitacion extends Model
{
    use HasFactory;

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
        return $this->belongsTo(Equipo::class, 'equipo');
    }
}
