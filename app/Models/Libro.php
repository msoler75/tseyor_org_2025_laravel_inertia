<?php

namespace App\Models;

use App\Pigmalion\StorageItem;
use App\Traits\EsCategorizable;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Laravel\Scout\Searchable;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Libro extends ContenidoBaseModel
{
    use CrudTrait;
    use EsCategorizable;
    use Searchable;

    // incluye la categoría 'todos'
    public $incluyeCategoriaTodos = 'Todos';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'imagen_lqip',
        'edicion',
        'paginas',
        'pdf',
        'visibilidad',
    ];

    public $table = 'libros';

    /**
     * Searchable: Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->titulo,
            'content' => $this->descripcion,
        ];
    }

    /**
     * Searchable: Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }

    /**
     * ContenidoBaseModel: obtiene el texto para el buscador global
     */
    /* public function getTextoContenidoBuscador()
    {
        // incluimos la descripcion breve
        return $this->descripcion;
    }*/

    /**
     * Después de guardar movemos los pdf
     */
    public function afterSave()
    {

        if (! $this->pdf) {
            return;
        }

        if (strpos($this->pdf, $this->getCarpetaMediosTemp(true)) !== false) {
            // hay que mover el pdf
            $pdfDest = $this->getCarpetaMedios(true).'/'.basename($this->pdf);
            $src = Storage::disk('public')->path($this->pdf);
            $dest = Storage::disk('public')->path($pdfDest);

            if (! file_exists($src)) {
                exit("archivo $src no existe");
            }

            Log::info('Libro con id: '.$this->id.", copiamos $src => $dest");
            copy($src, $dest);
            $this->pdf = Storage::disk('public')->url($pdfDest);

            // guardamos
            $this->saveQuietly();
        }
    }

    /**
     * Genera LQIP (placeholder) para la imagen de portada del libro.
     * Redimensiona a 170px de ancho manteniendo ratio, sharpen, JPEG quality 60.
     */
    public function generarLqip(): void
    {
        if (! $this->imagen) {
            return;
        }

        try {
            // Strip domain if imagen is a full URL
            $path = preg_replace('#^https?://[^/]+#', '', $this->imagen);

            $sti = new StorageItem($path);
            if (! $sti->exists()) {
                return;
            }

            // Normalize imagen to path-only (no domain)
            $this->imagen = $path;

            $manager = new ImageManager(new Driver);
            $image = $manager->read($sti->path);
            $currentWidth = $image->width();
            $image->scale(width: 170);
            if ($image->width() < $currentWidth || $image->height() < $currentWidth) {
                $image->sharpen(15);
            }
            $blob = $image->toJpeg(quality: 60)->toFilePointer();
            $this->imagen_lqip = 'data:image/jpeg;base64,'.base64_encode(stream_get_contents($blob));
        } catch (\Throwable $e) {
            Log::warning("No se pudo generar LQIP para #{$this->id}: {$e->getMessage()}");
        }
    }

    // SEO

    public function getDynamicSEOData(): SEOData
    {
        $image = $this->imagen ? url('/mockup/libro'.$this->imagen) : config('seo.image.fallback');

        return new SEOData(
            title: $this->titulo ?? $this->nombre ?? $this->name && null,
            description: $this->descripcion ?? mb_substr(strip_tags($this->texto ?? ''), 0, 400 - 3),
            image: str_replace(' ', '%20', $image),
            author: $this->autor ?? 'tseyor',
            published_time: Carbon::createFromFormat('Y-m-d H:i:s', $this->published_at ?? $this->created_at) ?? null,
            section: $this->categoria ?? ''
            // tags:
            // schema:
        );
    }
}
