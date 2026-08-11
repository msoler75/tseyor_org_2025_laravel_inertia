<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class RadioItem extends Model
{
    use CrudTrait;
    use RevisionableTrait;
    use SoftDeletes;

    protected $table = 'radio';

    protected $fillable = [
        'titulo',
        'url',
        'duracion',
        'categoria',
        'desactivado',
    ];

    // revisionable se aplica a nuevos contenidos
    protected $revisionCreationsEnabled = true;

    // Accesor

    public function getArchivoAttribute()
    {
        return basename($this->audio);
    }
}
