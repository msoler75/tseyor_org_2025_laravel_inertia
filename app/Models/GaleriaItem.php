<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Galeria;
use App\Models\Nodo;
use App\Models\User;

class GaleriaItem extends Model
{
    use HasFactory;

    protected $fillable = ['galeria_id', 'nodo_id', 'titulo', 'descripcion', 'user_id', 'orden'];

    public function galeria()
    {
        return $this->belongsTo(Galeria::class);
    }

    public function nodo()
    {
        return $this->belongsTo(Nodo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
