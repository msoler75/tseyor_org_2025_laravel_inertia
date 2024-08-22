<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Str;

class Grupo extends Model
{
    use CrudTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $revisionCreationsEnabled = true;


    // cuando se crea un usuario, llenaremos el campo "frase" con una frase aleatoria desde un archivo de texto
    public static function boot()
    {
        parent::boot();

        static::saving(function ($grupo) {
            if (!$grupo->slug)
                $grupo->slug = Str::slug($grupo->nombre);
        });
    }

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'grupo_user', 'group_id', 'user_id')
            ->using(Pertenencia::class)
            ->withPivot(['user_id'])
            ->withTimestamps();
    }

    // ACCESOR

    public function getUsuariosJSONAttribute()
    {
        $users = $this->usuarios()->select('users.id', 'users.name', 'users.email')->get();
        $usersWithoutPivot = $users->map(function ($user) {
            return [
                'value' => $user->id,
                'label' => $user->name //"{$user->name} <{$user->email}>"
            ];
        });
        return $usersWithoutPivot->toJson();
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
