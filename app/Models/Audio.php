<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Illuminate\Support\Facades\Log;

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

    // cuando se guarde el audio:
    public static function boot()
    {
        parent::boot();

        // corregimos la ruta del audio
        static::saved(function ($model) {
            if(str_starts_with($model->audio, "medios")) {
                $model->audio = "/almacen/".$model->audio;
                $model->saveQuietly(); // guardamos sin generar eventos
            }
        });
    }
}
