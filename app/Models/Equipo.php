<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\SEOModel;


class Equipo extends SEOModel
{
    use CrudTrait;
    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'categoria',
        'user_id',
        'group_id',
        'anuncio',
        'reuniones',
        'informacion'
    ];

    public function miembros()
    {
        return $this->belongsToMany(User::class, 'equipo_user')
            ->using(Membresia::class)
            ->withPivot('rol')
            ->withTimestamps();
    }

    public function coordinadores()
    {
        return $this->belongsToMany(User::class, 'equipo_user')
            ->using(Membresia::class)
            ->withPivot('rol')
            ->where('rol', 'coordinador');
    }

    public function creador() // creador del equipo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'group_id', 'id');
    }

    // carpetas del equipo
    public function carpetas()
    {
        return $this->hasMany(Carpeta::class, 'group_id', 'id');
    }


    // accesors
    public function getCreadorNombreAttribute()
    {
        return $this->creador->name;
    }


    // métodos
    public function esCoordinador($user_id)
    {
        return $this->coordinadores->contains(function ($coordinador) use ($user_id) {
            return $coordinador->id === $user_id;
        });
    }


    public function esMiembro($user_id)
    {
        return $this->miembros->contains(function ($miembro) use ($user_id) {
            return $miembro->id === $user_id;
        });
    }



    public function otorgarPermisosCarpetas($idUsuario) {
        // otorgamos permisos al usuario para administrar las carpetas del equipo
        foreach ($this->carpetas as $carpeta) {
            Permiso::updateOrCreate(
                [
                    'user_id' => $idUsuario,
                    'modelo' => 'nodos',
                    'modelo_id' => $carpeta->id,
                    'verbos' => 'leer,escribir,ejecutar'
                ]
            );
        }
    }

    public function removerPermisosCarpetas($idUsuario) {
        foreach ($this->carpetas as $carpeta) {
            $permiso = Permiso::where([
                'user_id' => $idUsuario,
                'modelo' => 'nodos',
                'modelo_id' => $carpeta->id,
            ])->first();

            if ($permiso) {
                $permiso->delete();
            }
        }
    }

}


class Membresia extends Pivot
{
    protected $table = 'equipo_user';

    protected $casts = [
        'user_id' => 'integer',
        'equipo_id' => 'integer',
    ];
}
