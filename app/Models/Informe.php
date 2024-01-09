<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoConAudios;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;


class Informe extends ContenidoConAudios
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    protected $table = 'informes';

    protected $fillable = [
        'titulo',
        'categoria',
        'equipo_id',
        'descripcion',
        'texto',
        'audios',
        'visibilidad',
    ];


    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'equipo_id', 'id');
    }


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
        $equipo = $this->equipo;
        return "{$equipo->nombre} - $fecha $tipo" . ($multiple ? " " . ('a' + $index) : "") . ".mp3";
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
            'id' => $this->id,
            // <- Always include the primary key
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'content' => $this->texto,
        ];
    }
}
