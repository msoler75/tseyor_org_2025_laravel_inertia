<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class RadioItem extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use CrudTrait;

    protected $table = "radio";

    protected $fillable = [
        'titulo',
        'url',
        'duracion',
        'categoria',
        'desactivado'
    ];

    // revisionable se aplica a nuevos contenidos
    protected $revisionCreationsEnabled = true;

    // Accesor

    public function getArchivoAttribute() {
        return basename($this->audio);
    }

}
