<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\SEOModel;
use Laravel\Scout\Searchable;


class Comunicado extends SEOModel
{
    use CrudTrait;
    use Searchable;

    protected $fillable = [
        'titulo',
        'numero',
        'categoria',
        'descripcion',
        'texto',
        'imagen',
        'fechaComunicado',
        'visibilidad',
        'pdf',
        'audios',
        'slug',
    ];

    // hooks del modelo
    protected static function booted()
    {
        // cuando se guarda el item
        static::saving(function ($comunicado) {
            // $comunicado->slug = "heidi2";
        });
    }

    /* public static function search($term)
    {
        return static::query()
            ->where('titulo', 'LIKE', "%{$term}%")
            ->orWhere('texto', 'LIKE', "%{$term}%")
            ->orWhere('numero', '=', "{$term}");
    }
    */



    // SCOUT


     /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }


   /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id, // <- Always include the primary key
            'titulo' => $this->titulo,
            'imagen' => $this->imagen,
            'descripcion' => $this->descripcion,
            'texto' => $this->texto,
            'categoria' => $this->categoria,
            'numero' => $this->numero,
            'fecha_comunicado' => $this->fecha_comunicado,
        ];
    }
}
