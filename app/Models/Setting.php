<?php

namespace App\Models;

use App\Models\SEOModel;


class Setting extends SEOModel
{

    protected $primaryKey = 'name';

    protected $fillable = [
        'name',
        'value',
    ];
}
