<?php

namespace App\Models;

use App\Services\AnuncioService;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Venturecraft\Revisionable\RevisionableTrait;

class Setting extends Model
{
    use CrudTrait;
    use RevisionableTrait;
    use SoftDeletes;

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
