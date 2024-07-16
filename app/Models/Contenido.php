<?php

namespace App\Models;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Contenido extends Model
{
    use Searchable;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'coleccion',
        'id_ref',
        'slug_ref',
        'titulo',
        'descripcion',
        'texto_busqueda',
        'imagen',
        'fecha',
        'visibilidad'
    ];

    protected $dates = [
        'fecha',
    ];

    /*
    public static function search($term)
    {
        return static::select(['coleccion', 'id_ref', 'descripcion', 'imagen', 'slug_ref', 'fecha'])
            ->where('titulo', 'LIKE', "%{$term}%")
            // ->orWhereRaw('MATCH (descripcion) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
            // ->orWhereRaw('MATCH (texto) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
            ->orWhere('descripcion', 'LIKE', "%{$term}%")
            ->orWhere('texto', 'LIKE', "%{$term}%")
            ->get();
    }
    */


    /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P' && !$this->deleted_at;
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
            // 'descripcion' => in_array($this->coleccion, $coleccionesConDescripcion) ? $this->descripcion : "",
            'texto_busqueda' => $this->texto_busqueda,
        ];
    }


    public function getUrlAttribute() {
        if($this->coleccion=='paginas')
        return "/". $this->slug_ref;
    return "/" . $this->coleccion . "/" . ($this->slug_ref ?  $this->slug_ref: $this->id_ref);
    }
}
