<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Pigmalion\Markdown;
use Carbon\Carbon;


class Evento extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'texto',
        'imagen',
        'published_at',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'visibilidad',
        'centro_id',
        'sala_id',
        'equipo_id'
    ];

    /**
     * Casts para atributos de fecha
     * Eloquent convertirá automáticamente estos campos a Carbon
     */
    protected $casts = [
        'published_at' => 'datetime',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    /**
     * Accessors para formatear fechas como strings sin hora
     */
    public function getFechaInicioAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function getFechaFinAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }


    public function centro() // centro presencial donde transcurre el evento
    {
        return $this->belongsTo(Centro::class, 'centro_id');
    }

    public function sala() // sala virtual donde transcurre el evento
    {
        return $this->belongsTo(Sala::class, 'sala_id');
    }

    public function equipo() // equipo que organiza el evento
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    // SCOUT

    /**
     * Solo se indexa si está publicado
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
        // Normalizar fecha inicio: puede venir como string o como DateTime/Carbon.
        $fechaInicio = null;

        try {
            if ($this->fecha_inicio) {
                $fechaInicio = $this->fecha_inicio instanceof \DateTimeInterface
                    ? $this->fecha_inicio
                    : Carbon::parse($this->fecha_inicio);
            }
        } catch (\Throwable $e) {
            $fechaInicio = null;
        }

        return [
            'id' => $this->id,
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'content' => Markdown::removeMarkdown($this->texto),
            'categoria' => $this->categoria,
            'fecha_inicio' => $fechaInicio ? $fechaInicio->format('Y-m-d H:i:s') : null,
            'ano' => $fechaInicio ? $fechaInicio->format('Y') : null,
            'mes' => $fechaInicio ? $fechaInicio->format('m') : null,
        ];
    }
}
