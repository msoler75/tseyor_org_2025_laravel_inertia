<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;

class Lugar extends ContenidoBaseModel
{
    use CrudTrait;
    use HasFactory;
    use Searchable;

    protected $table = 'lugares';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'categoria',
        'imagen',
        'texto',
        'libros',
        'relacionados',
        'visibilidad'
    ];

    /**
     * Solo se indexa si acaso está publicado
     */
    public function shouldBeSearchable(): bool
    {
        return $this->visibilidad == 'P';
    }

}
