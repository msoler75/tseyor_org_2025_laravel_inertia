<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;


class Setting extends Model
{
    use CrudTrait;

    protected $fillable = [
        'name',
        'description',
        'value',
    ];
}
