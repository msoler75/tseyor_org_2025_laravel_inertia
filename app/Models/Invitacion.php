<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Invitacion extends Model
{
    use CrudTrait;

    protected $table = 'invitaciones';

    protected $fillable = [
        'equipo_id',
        'user_id',
        'email',
        'token',
        'estado',
        'error',
        'accepted_at',
        'declined_at',
        'sent_at'
    ];

    protected $dates = [
        'accepted_at',
        'declined_at',
        'sent_at',
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
