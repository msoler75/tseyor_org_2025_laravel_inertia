<?php

namespace App\Models;

use App\Contracts\PdfGenerable;
use App\Traits\BuscableTrait;
use App\Traits\ComunicadoMediaTrait;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Venturecraft\Revisionable\RevisionableTrait;

/**
 * Comunicados de Interiorización — contenido restringido a miembros del equipo "iniciados-interiorizacion".
 *
 * NO extiende ContenidoBaseModel para evitar que se copie a la tabla `contenidos`
 * (buscador global). Tiene su propio índice Scout.
 */
class ComunicadoInteriorizacion extends Model implements PdfGenerable
{
    use BuscableTrait;
    use ComunicadoMediaTrait;
    use CrudTrait;
    use RevisionableTrait;
    use Searchable;
    use SoftDeletes;

    protected $table = 'comunicados_interiorizacion';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'texto',
        'nivel',
        'ciclo',
        'numero',
        'fecha_comunicado',
        'ano',
        'imagen',
        'audios',
        'visibilidad',
    ];

    protected $casts = [
        'audios' => 'array',
        'nivel' => 'integer',
        'fecha_comunicado' => 'date',
    ];

    public $sortable = [
        'fecha_comunicado',
    ];

    // --- Boot: hooks propios (sin ContenidoHelper) ---

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->slug) && ! empty($model->titulo)) {
                $model->slug = \Illuminate\Support\Str::slug($model->titulo);
            }

            if (isset($model->fecha_comunicado)) {
                $model->ano = date('Y', strtotime($model->fecha_comunicado));
            }
        });

        static::updated(function ($model) {
            $pdf_path = storage_path('app/public/comunicados_interiorizacion/'.$model->id.'.pdf');
            if (file_exists($pdf_path)) {
                unlink($pdf_path);
            }
        });
    }

    // --- Scope: solo contenido visible para el usuario actual ---

    /**
     * Filtra comunicados que el usuario puede ver:
     * - Públicos (visibilidad P) siempre
     * - Todos si es admin o miembro del equipo de interiorización
     */
    public function scopeParaUsuario($query, $user = null)
    {
        $user = $user ?? auth()->user();

        if ($user && ($user->can('administrar contenidos') || $user->esIniciado())) {
            return $query;
        }

        return $query->where('visibilidad', 'P');
    }

    // --- Scout: búsqueda local dentro de esta sección ---

    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P' && ! $this->deleted_at;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->titulo,
            'description' => $this->descripcion,
            'content' => strip_tags($this->texto ?? ''),
            'nivel' => $this->nivel,
            'ciclo' => $this->ciclo,
            'ano' => $this->ano,
        ];
    }

    // --- Accessors ---

    public function getNivelNombreAttribute(): string
    {
        return match ($this->nivel) {
            1 => 'Nivel 1',
            2 => 'Nivel 2',
            default => 'General',
        };
    }

    // --- PDF ---

    public function getPdfFilenameAttribute(): string
    {
        return $this->titulo.' - TSEYOR Interiorización.pdf';
    }

    public function getPdfPathAttribute(): string
    {
        return 'pdf/comunicados_interiorizacion/'.$this->pdf_filename;
    }

    public function generatePdf()
    {
        return \App\Services\PDFGenerator::generatePdf($this, 'contenido-sin-titulo-pdf');
    }

    // --- PdfGenerable interface ---

    public function getPdfPath(): string
    {
        return $this->pdfPath;
    }

    public function getPdfFilename(): string
    {
        return $this->pdf_filename;
    }

    public function getTituloPdf(): string
    {
        return $this->titulo ?? '';
    }

    public function getTextoPdf(): string
    {
        return $this->texto ?? '';
    }

    public function getImagenPdf(): ?string
    {
        return $this->imagen;
    }

    public function getUpdatedAtTimestamp(): int
    {
        return $this->updated_at->getTimestamp();
    }
}
