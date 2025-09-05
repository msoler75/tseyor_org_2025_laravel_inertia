<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = [
        'user_id',
        'coleccion',
        'id_ref',
    ];

    // No usamos timestamps en esta tabla
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar por coleccion + id_ref
     */
    public function scopeOfReference($query, $coleccion, $id_ref)
    {
        return $query->where('coleccion', $coleccion)->where('id_ref', $id_ref);
    }
}
