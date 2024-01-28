<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Grupo extends Model
{
    use CrudTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'nombre',
        'slug',
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'grupo_user', 'group_id', 'user_id')
            ->using(Pertenencia::class)
            ->withPivot(['user_id'])
            ->withTimestamps();
    }
}

class Pertenencia extends Pivot
{
    protected $table = 'grupo_user';

    protected $casts = [
        'user_id' => 'integer',
        'group_id' => 'integer',
    ];
}
