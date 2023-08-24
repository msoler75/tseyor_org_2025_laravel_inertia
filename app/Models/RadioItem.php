<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class RadioItem extends Model
{
    use CrudTrait;
    protected $table = "radio";

    protected $fillable = [
        'audio',
        'duracion',
        'categoria',
        'desactivado'
    ];


    // Accesor

    public function getArchivoAttribute() {
        return basename($this->audio);
    }
}
