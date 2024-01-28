<?php

namespace App\Traits;

// use Illuminate\Support\Facades\Storage;
use App\Pigmalion\ContenidoHelper;


// EL CODIGO DE ESTE TRAIT SE HA LLEVADO AL MODELO CONTENIDOBASEMODEL
trait EsContenido
{
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Acciones antes de guardar el modelo
            ContenidoHelper::rellenarSlugImagenYDescripcion($model);
        });

        static::saved(function ($model) {
            // Acciones después de que el modelo se haya guardado
            ContenidoHelper::guardarContenido($model->table, $model);
        });

        static::deleting(function ($model) {
            // dd("deleting");
            //ContenidoHelper::removerContenido($model);
            // Acciones antes de borrar el modelo
            // ...
        });

        static::deleted(function ($model) {
            //dd("deleted", $model);
            ContenidoHelper::removerContenido($model);
            // Acciones después de que el modelo se haya borrado
            // ...
        });
    }




}
