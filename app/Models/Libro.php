<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Cache;

class Libro extends ContenidoBaseModel
{

    use CrudTrait;
    use Searchable;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'edicion',
        'paginas',
        'pdf',
        'visibilidad'
    ];

    public $table = 'libros';

    /**
     * Para el controlador de libros.
     * Elimina la cache de catgorias cuando hay cambios en algun libro
     */
    public static function boot()
    {
        parent::boot();

        static::saved(function ($model) {
            Cache::forget('libros_categorias');
        });

        static::deleted(function ($model) {
            Cache::forget('libros_categorias');
        });
    }


    /**
     * Searchable: Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion
        ];
    }

    /**
     * Searchable: Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }


    /**
     * ContenidoBaseModel: obtiene el texto para el buscador global
     */
    /* public function getTextoContenidoBuscador()
    {
        // incluimos la descripcion breve
        return $this->descripcion;
    }*/


}
