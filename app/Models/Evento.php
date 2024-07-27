<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;


class Evento extends ContenidoBaseModel
{
    use CrudTrait;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'texto',
        'imagen',
        'published_at',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'visibilidad',
        'centro_id',
        'sala_id',
        'equipo_id'
    ];

    protected $dates = [
        'published_at',
        'fecha_inicio',
        'fecha_fin',
    ];


    public function centro() // centro presencial donde transcurre el evento
    {
        return $this->belongsTo(Centro::class, 'centro_id');
    }

    public function sala() // sala virtual donde transcurre el evento
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    public function equipo() // equipo que organiza el evento
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }
}
