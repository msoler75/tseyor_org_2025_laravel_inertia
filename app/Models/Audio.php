<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;


class Audio extends ContenidoBaseModel
{
    use CrudTrait;
    /**
     * El nombre de la tabla asociada al modelo.
     *
     * @var string
     */
    protected $table = 'audios';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'enlace',
        'audio',
        'visibilidad',
        'duracion'
    ];
}
