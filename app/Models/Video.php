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

    protected static function booted()
    {
        static::saving(function ($video) {
            if (is_null($video->orden)) {
                return;
            }

            $original = $video->getOriginal('orden');

            if (! $video->exists) {
                static::where('orden', '>=', $video->orden)->increment('orden');
            } elseif ($video->orden != $original) {
                if ($video->orden > $original) {
                    static::where('id', '!=', $video->id)
                        ->where('orden', '>', $original)
                        ->where('orden', '<=', $video->orden)
                        ->decrement('orden');
                } else {
                    static::where('id', '!=', $video->id)
                        ->where('orden', '>=', $video->orden)
                        ->where('orden', '<', $original)
                        ->increment('orden');
                }
            }
        });

        static::saved(function ($video) {
            if (is_null($video->orden)) {
                return;
            }
            // Recompactar para eliminar cualquier hueco
            static::reordenar();
        });
    }

    public static function reordenar()
    {
        $videos = static::whereNotNull('orden')->orderBy('orden')->get();
        $i = 1;
        foreach ($videos as $v) {
            if ($v->orden != $i) {
                \DB::table('videos')->where('id', $v->id)->update(['orden' => $i]);
            }
            $i++;
        }
    }

    /**
     * Botón para mover el video hacia arriba en el orden
     */
    public function getMoveUpButton()
    {
        if (is_null($this->orden)) {
            return '';
        }

        $previousVideo = static::where('orden', '<', $this->orden)
            ->orderBy('orden', 'desc')
            ->first();

        if (! $previousVideo) {
            return '';
        }

        return '<a class="btn btn-sm btn-outline-secondary"
                   href="'.url('admin/video/'.$this->id.'/move-up').'"
                   title="Mover hacia arriba">
                   <i class="las la-arrow-up"></i>
                </a>';
    }

    /**
     * Botón para mover el video hacia abajo en el orden
     */
    public function getMoveDownButton()
    {
        if (is_null($this->orden)) {
            return '';
        }

        $nextVideo = static::where('orden', '>', $this->orden)
            ->orderBy('orden', 'asc')
            ->first();

        if (! $nextVideo) {
            return '';
        }

        return '<a class="btn btn-sm btn-outline-secondary"
                   href="'.url('admin/video/'.$this->id.'/move-down').'"
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
