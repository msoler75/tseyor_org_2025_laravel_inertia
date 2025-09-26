<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class EnlaceCorto extends Model
{
    use HasFactory;

    protected $table = 'enlaces_cortos';

    protected $fillable = [
        'codigo',
        'url_original',
        'prefijo',
        'titulo',
        'descripcion',
        'contenido_id',

        // Campos SEO
        'meta_titulo',
        'meta_descripcion',
        'meta_keywords',
        'og_titulo',
        'og_descripcion',
        'og_imagen',
        'og_tipo',
        'twitter_card',
        'twitter_titulo',
        'twitter_descripcion',
        'twitter_imagen',
        'canonical_url',

        // Estadísticas y estados
        'clics',
        'ultimo_clic',
        'activo',
    ];

    protected $casts = [
        'clics' => 'integer',
        'ultimo_clic' => 'datetime',
        'activo' => 'boolean',
    ];

    /**
     * Contenido relacionado (para sincronización automática)
     */
    public function contenido(): BelongsTo
    {
        return $this->belongsTo(Contenido::class);
    }

    /**
     * Scope para enlaces activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Obtener la URL completa del enlace corto
     */
    public function getUrlCortaAttribute(): string
    {
        $domain = config('enlaces_cortos.dominios.principal');
        return rtrim($domain, '/') . '/' . $this->prefijo . '/' . $this->codigo;
    }

    /**
     * Obtener la URL de estadísticas en Google Analytics
     */
    public function getUrlAnalyticsAttribute(): string
    {
        // URL para ver estadísticas específicas de este enlace en GA
        return "/analytics/short-links/{$this->prefijo}/{$this->codigo}";
    }

    /**
     * Incrementar contador de clics
     */
    public function incrementarClics(): void
    {
        $this->increment('clics');
        $this->update(['ultimo_clic' => now()]);
    }

    /**
     * Verificar si el enlace es válido para redirección
     */
    public function esValidoParaRedirigir(): bool
    {
        return $this->activo;
    }

    /**
     * Obtener metadatos SEO como array
     */
    public function getSeoDataAttribute(): array
    {
        return [
            'title' => $this->meta_titulo ?: $this->titulo,
            'description' => $this->meta_descripcion ?: $this->descripcion,
            'keywords' => $this->meta_keywords,
            'canonical' => $this->canonical_url ?: $this->url_original,
            'og' => [
                'title' => $this->og_titulo ?: $this->titulo,
                'description' => $this->og_descripcion ?: $this->descripcion,
                'image' => $this->og_imagen,
                'type' => $this->og_tipo,
                'url' => $this->url_corta,
            ],
            'twitter' => [
                'card' => $this->twitter_card,
                'title' => $this->twitter_titulo ?: $this->titulo,
                'description' => $this->twitter_descripcion ?: $this->descripcion,
                'image' => $this->twitter_imagen,
            ],
        ];
    }
}
