<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use App\Services\AnuncioService;

class Setting extends Model
{
    use CrudTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'value',
    ];

    protected static function booted()
    {
        static::saved(function () {
            app(AnuncioService::class)->clearCache();
        });

        static::deleted(function () {
            app(AnuncioService::class)->clearCache();
        });
    }
}
