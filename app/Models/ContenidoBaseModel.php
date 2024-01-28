<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;
use Carbon\Carbon;
use App\Traits\EsContenido;

/*
 ContenidoBaseModel es un modelo básico que sirve para:
 - SEO
 - Gestionar una base para los contenidos con el trait EsContenido
 */

class ContenidoBaseModel extends Model
{
    use HasSEO;
    use EsContenido;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $revisionCreationsEnabled = true;

    // revisionable identifier
    public function identifiableName()
    {
        return $this->titulo ?? $this->nombre ?? $this->name;
    }

    // If you are using another bootable trait
    // be sure to override the boot method in your model
    public static function boot()
    {
        parent::boot();
    }

    // https://github.com/ralphjsmit/laravel-seo
    public function getDynamicSEOData(): SEOData
    {
        $image = $this->imagen ? url($this->imagen) : config('seo.image.fallback');
        return new SEOData(
            title: $this->titulo ?? $this->nombre ?? $this->name && null,
            description: $this->descripcion ?? mb_substr(strip_tags($this->texto ?? ""), 0, 400 - 3),
            image: $image,
            author: $this->autor ?? 'tseyor',
            published_time: Carbon::createFromFormat('Y-m-d H:i:s', $this->published_at ?? $this->created_at) ?? null,
            section: $this->categoria ?? ''
            // tags:
            // schema:
        );
    }


    /**
     * Función heredable para cada modelo, sirve para indicarle al buscador global qué texto o palabras clave indexan este contenido
     * (además de los campos de título o nombre)
     */
    public function getTextoContenidoBuscador()
    {
        return null;
    }
}
