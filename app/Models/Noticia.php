<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;


class Noticia extends Model
{
    use HasSEO;

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'texto',
        'imagen',
        'published_at',
        'visibilidad'
    ];

    protected $dates = [
        'published_at',
    ];

    public static function search($term)
    {
        return static::query()
            ->where('visibilidad', 'P')
            ->where(function ($query) use ($term) {
                $query->where('titulo', 'LIKE', "%{$term}%")
                    ->orWhere('descripcion', 'LIKE', "%{$term}%")
                    ->orWhere('texto', 'LIKE', "%{$term}%");
            });
    }


    // https://github.com/ralphjsmit/laravel-seo
    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->titulo,
            description: $this->descripcion,
            image: url($this->imagen),
            author: 'tseyor',
            // published_time: $this->published_at,
            // section: $this->categoria
            // tags:
            // schema:
        );
    }
}
