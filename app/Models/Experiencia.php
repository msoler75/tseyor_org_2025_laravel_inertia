<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Models\ContenidoBaseModel;
use App\Traits\EsCategorizable;

class Experiencia extends ContenidoBaseModel
{
    use CrudTrait;
    use EsCategorizable;

    protected $table = 'experiencias';

    protected $fillable = [
        'nombre',
        'fecha',
        'lugar',
        'categoria',
        'texto',
        'visibilidad',
        'user_id', // creador de la experiencia, en el caso de recopilación de experiencias grupales
    ];

      public static function search($term)
    {
        return static::query()
            ->where('nombre', 'LIKE', "%{$term}%")
            ->orWhere('texto', 'LIKE', "%{$term}%");
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
