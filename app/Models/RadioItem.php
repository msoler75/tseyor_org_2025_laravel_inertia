<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class RadioItem extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use CrudTrait;

    protected $table = "radio";

    protected $fillable = [
        'titulo',
        'url',
        'duracion',
        'categoria',
        'desactivado'
    ];


    // Accesor

    public function getArchivoAttribute() {
        return basename($this->audio);
    }

}
