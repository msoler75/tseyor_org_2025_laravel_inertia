<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Laravel\Scout\Searchable;

class Video extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;

    /*
    |--------------------------------------------------------------------------
    | GLOBAL VARIABLES
    |--------------------------------------------------------------------------
    */

    protected $table = 'videos';
    // protected $primaryKey = 'id';
    // public $timestamps = false;
    protected $guarded = ['id'];
    protected $fillable = ['titulo', 'slug', 'descripcion', 'enlace', 'visibilidad', 'orden'];
    // protected $hidden = [];

    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */

    /**
     * Botón para mover el video hacia arriba en el orden
     */
    public function getMoveUpButton()
    {
        $previousVideo = static::where('orden', '<', $this->orden)
            ->orderBy('orden', 'desc')
            ->first();

        if (!$previousVideo) {
            return '';
        }

        return '<a class="btn btn-sm btn-outline-secondary"
                   href="' . url('admin/video/' . $this->id . '/move-up') . '"
                   title="Mover hacia arriba">
                   <i class="las la-arrow-up"></i>
                </a>';
    }

    /**
     * Botón para mover el video hacia abajo en el orden
     */
    public function getMoveDownButton()
    {
        $nextVideo = static::where('orden', '>', $this->orden)
            ->orderBy('orden', 'asc')
            ->first();

        if (!$nextVideo) {
            return '';
        }

        return '<a class="btn btn-sm btn-outline-secondary"
                   href="' . url('admin/video/' . $this->id . '/move-down') . '"
                   title="Mover hacia abajo">
                   <i class="las la-arrow-down"></i>
                </a>';
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    /**
     * Scope para ordenar por el campo orden
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('orden', 'ASC')->orderBy('created_at', 'DESC');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
