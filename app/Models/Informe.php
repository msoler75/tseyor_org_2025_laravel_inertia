<?php

namespace App\Models;

use App\Pigmalion\Markdown;
use App\Traits\EsCategorizable;
use App\Traits\TieneArchivos;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;

class Informe extends ContenidoConAudios
{
    use CrudTrait;
    use EsCategorizable;
    use Searchable;
    use TieneArchivos;

    protected $table = 'informes';

    protected $fillable = [
        'titulo',
        'categoria',
        'equipo_id',
        'descripcion',
        'texto',
        'audios',
        'archivos',
        'visibilidad',
    ];

    protected $casts = [
        'audios' => 'array',
        'archivos' => 'array',
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
        $equipo = $this->equipo;
        $multiple = count($this->audios) > 1;
        $original = $this->audios[$index];
        $orig_filename = pathinfo($original, PATHINFO_FILENAME);

        // return "{$equipo->nombre} - $fecha $tipo " . ($multiple ? "($index) " : "") . $orig_filename . ".mp3";
        return $orig_filename.'.mp3';
    }

    // ACCESSORS

    public function getEquipoNombreAttribute()
    {
        return $this->equipo ? $this->equipo->nombre : '<no definido>';
    }

    // SCOUT

    /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return true; // incluso los borradores se pueden buscar por los coordinadores
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
            'content' => Markdown::removeMarkdown($this->texto),
        ];
    }
}
