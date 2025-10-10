<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Illuminate\Support\Facades\Log;
use Laravel\Scout\Searchable;
use App\Pigmalion\StorageItem;

class Audio extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
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
            \Log::info("Audio saved: ", ['model' => $model]);
            if (str_starts_with($model->audio, "medios")) {
                $model->audio = "/almacen/" . $model->audio;
                $model->saveQuietly(); // guardamos sin generar eventos
            }

            // borrar todos los archivos en la carpeta excepto el actual
            $folder = $model->getCarpetaMedios();
            $filename = basename($model->audio);
            $loc = new StorageItem($folder);
            $path = $loc->path;
            $files = glob($path."/*");
            foreach ($files as $file) {
                if (basename($file) != $filename) {
                    unlink($file);
                }
            }
        });
    }



    // SCOUT

    /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P' && !$this->deleted_at;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->titulo,
            'categoria' => $this->categoria
        ];
    }
}
