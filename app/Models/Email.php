<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    use CrudTrait;

    protected $fillable = [
        'from',
        'to',
        'subject',
        'body'
    ];



}
