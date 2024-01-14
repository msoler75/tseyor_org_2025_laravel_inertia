<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;


class Meditacion extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    // incluye la categoría 'todas'
    public $incluyeCategoriaTodos = "Todas";

    protected $table = 'meditaciones';

    protected $fillable = [
        'titulo',
        'slug',
        'categoria',
        'descripcion',
        'texto',
        'audios',
        'visibilidad',
    ];


     // ContenidoConAudios

    /**
     * Nombre de los archivos de audio
     **/
    public function generarNombreAudio($index)
    {
        $tipo = $this->categoria;
        $fecha = $this->created_at->format('ymd');
        $audios = $this->obtenerAudiosArray();
        $multiple = count($audios) > 1;
        return $this->nombre . ($multiple ? " " . ('a' + $index) : "") . ".mp3";
    }

    /**
     * En qué carpeta se guardarán los audios
     **/
    public function generarRutaAudios()
    {
        $año = $this->created_at->year;
        return "medios/informes/{$this->equipo->slug}/$año";
    }



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
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'content' => $this->texto,
        ];
    }
}
