<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Pigmalion\Markdown;
use App\Pigmalion\Countries;
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
        'fechas_evento',
        'pais',
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


    // ACCESOR
     public function getNombrePaisAttribute()
     {
         return Countries::getCountry($this->pais);
     }

    /**
     * Accessors para formatear fechas como strings sin hora
     */
    public function getFechaInicioAttribute($value)
    {
        // Si hay un grupo de fechas (evento repetible), retornar la primera fecha del conjunto
        try {
            $fechas = $this->getFechasEventoNormalized();
            if (!empty($fechas)) {
                return Carbon::parse($fechas[0])->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            // fallthrough
        }

        return $value ? Carbon::parse($value)->format('Y-m-d') : null;
    }

    public function getFechaFinAttribute($value)
    {
        // Si hay un grupo de fechas (evento repetible), retornar la última fecha del conjunto
        try {
            $fechas = $this->getFechasEventoNormalized();
            if (!empty($fechas)) {
                return Carbon::parse($fechas[count($fechas) - 1])->format('Y-m-d');
            }
        } catch (\Throwable $e) {
            // fallthrough
        }

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

        $fechaInicioCarbon = null;

        try {
            $first = $this->firstDate();
            $fechaInicioCarbon = $first instanceof \DateTimeInterface ? $first : ($first ? Carbon::parse($first) : null);
        } catch (\Throwable $e) {
            $fechaInicioCarbon = null;
        }

        return [
            'id' => $this->id,
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'content' => Markdown::removeMarkdown($this->texto),
            'categoria' => $this->categoria,
            'fecha_inicio' => $fechaInicioCarbon ? $fechaInicioCarbon->format('Y-m-d H:i:s') : null,
            'ano' => $fechaInicioCarbon ? $fechaInicioCarbon->format('Y') : null,
            'mes' => $fechaInicioCarbon ? $fechaInicioCarbon->format('m') : null,
        ];
    }

    /**
     * Normaliza y devuelve el array de fechas configuradas en `fechas_evento`.
     * Acepta JSON, array o string con separador por comas.
     * Devuelve array de strings en formato Y-m-d ordenadas asc.
     *
     * @return array
     */
    public function getFechasEventoNormalized(): array
    {
        $raw = $this->attributes['fechas_evento'] ?? null;

        if (is_null($raw)) {
            return [];
        }

        $arr = [];

        if (is_array($raw)) {
            $arr = $raw;
        } elseif (is_string($raw)) {
            // intentar json_decode
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $arr = $decoded;
            } else {
                // asumir separador por comas
                $arr = array_filter(array_map('trim', explode(',', $raw)));
            }
        }

        $normalized = [];
        foreach ($arr as $item) {
            if (empty($item)) continue;
            try {
                $c = Carbon::parse($item)->format('Y-m-d');
                $normalized[] = $c;
            } catch (\Throwable $e) {
                // ignorar entradas inválidas
            }
        }

        sort($normalized);

        return array_values($normalized);
    }

    /**
     * Devuelve un array de instancias Carbon a partir del conjunto de fechas.
     *
     * @return Carbon[]
     */
    public function fechasEventoCarbon(): array
    {
        $fechas = $this->getFechasEventoNormalized();
        $out = [];
        foreach ($fechas as $f) {
            try {
                $out[] = Carbon::parse($f)->startOfDay();
            } catch (\Throwable $e) {
            }
        }
        return $out;
    }

    /**
     * Primera fecha (earliest) del evento: si hay `fechas_evento` usa la primera, si no `fecha_inicio`.
     *
     * @return Carbon|null|string
     */
    public function firstDate()
    {
        $list = $this->fechasEventoCarbon();
        if (!empty($list)) return $list[0];
        return $this->attributes['fecha_inicio'] ?? null;
    }

    /**
     * Última fecha (latest) del evento: si hay `fechas_evento` usa la última, si no `fecha_fin`.
     *
     * @return Carbon|null|string
     */
    public function lastDate()
    {
        $list = $this->fechasEventoCarbon();
        if (!empty($list)) return $list[count($list) - 1];
        return $this->attributes['fecha_fin'] ?? null;
    }

    /**
     * Próxima fecha futura (hoy incluido). Devuelve Carbon o null.
     *
     * @return Carbon|null
     */
    public function nextDate()
    {
        $today = Carbon::today();
        foreach ($this->fechasEventoCarbon() as $d) {
            if ($d->greaterThanOrEqualTo($today)) return $d;
        }

        // si no hay fechas_evento, y existe fecha_inicio, comprobarla
        try {
            if ($this->attributes['fecha_inicio']) {
                $f = Carbon::parse($this->attributes['fecha_inicio'])->startOfDay();
                if ($f->greaterThanOrEqualTo($today)) return $f;
            }
        } catch (\Throwable $e) {
        }

        return null;
    }

    /**
     * Determina si el evento está en curso (hoy entre primera y última fecha inclusive).
     *
     * @return bool
     */
    public function isOngoing(): bool
    {
        $first = $this->firstDate();
        $last = $this->lastDate();

        try {
            $start = $first instanceof \DateTimeInterface ? Carbon::parse($first)->startOfDay() : ($first ? Carbon::parse($first)->startOfDay() : null);
            $end = $last instanceof \DateTimeInterface ? Carbon::parse($last)->endOfDay() : ($last ? Carbon::parse($last)->endOfDay() : null);
            if ($start && $end) {
                $now = Carbon::now();
                return $now->between($start, $end);
            }
        } catch (\Throwable $e) {
        }

        return false;
    }

    /**
     * Determina si el evento tiene una próxima fecha (hoy o futura).
     *
     * @return bool
     */
    public function isUpcoming(): bool
    {
        return $this->nextDate() !== null;
    }
}
