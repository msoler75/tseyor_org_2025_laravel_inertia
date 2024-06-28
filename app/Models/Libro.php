<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\DiskUtil;


class Libro extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    // incluye la categoría 'todos'
    public $incluyeCategoriaTodos = "Todos";

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'edicion',
        'paginas',
        'pdf',
        'visibilidad'
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
            'content' => $this->descripcion
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
    public function afterSave() {

        if (!$this->pdf) return;

        if(strpos($this->pdf, $this->getCarpetaMediosTemp(true)) !== false) {
            // hay que mover el pdf
            $pdfDest =  $this->getCarpetaMedios(true). '/' . basename($this->pdf);
            $src = Storage::disk('public')->path($this->pdf);
            $dest = Storage::disk('public')->path( $pdfDest );

            if(!file_exists($src))
                die("archivo $src no existe");

            Log::info("Libro con id: ".$this->id.", copiamos $src => $dest");
            copy($src, $dest);
            $this->pdf = Storage::disk('public')->url($pdfDest);

            // guardamos
            $this->saveQuietly();
        }

    }
}
