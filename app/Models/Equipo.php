<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Models\ContenidoBaseModel;
use Laravel\Scout\Searchable;
use App\Traits\EsCategorizable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Pigmalion\StorageItem;


class Equipo extends ContenidoBaseModel
{
    use CrudTrait;
    use Searchable;
    use EsCategorizable;

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'imagen',
        'categoria',
        'group_id',
        'anuncio',
        'reuniones',
        'informacion',
        'ocultarCarpetas',
        'ocultarArchivos',
        'ocultarMiembros',
        'ocultarSolicitudes'
    ];

    public static function boot()
    {
        parent::boot();

        Membresia::observe(MembresiaObserver::class); // observamos cambios en la membresía

        static::created(function ($equipo) {
            $slug = $equipo->slug ?? Str::slug($equipo->nombre);
            if (!$equipo->slug)
                $equipo->slug = $slug;

            // Crea un nuevo grupo con el mismo nombre del equipo
            $grupo = Grupo::create(['nombre' => $equipo->nombre, 'slug' => $slug]);

            $equipo->group_id = $grupo->id;
            $equipo->save();

            $equipo->crearCarpeta();
        });
    }

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

    /* public function creador() // creador del equipo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }*/

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'group_id', 'id');
    }

    // carpetas del equipo
    public function carpetas()
    {
        return $this->hasMany(NodoCarpeta::class, 'group_id', 'group_id');
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



    public function otorgarPermisosCarpetas($idUsuario)
    {
        // otorgamos permisos al usuario para administrar las carpetas del equipo
        foreach ($this->carpetas as $carpeta) {
            Acl::updateOrCreate(
                [
                    'user_id' => $idUsuario,
                    'nodo_id' => $carpeta->id,
                    'verbos' => 'leer,escribir,ejecutar'
                ]
            );
        }
        Acl::clearCache(User::find($idUsuario));
    }

    public function removerPermisosCarpetas($idUsuario)
    {
        foreach ($this->carpetas as $carpeta) {
            $permiso = Acl::where([
                'user_id' => $idUsuario,
                'nodo_id' => $carpeta->id,
            ])->first();

            if ($permiso) {
                $permiso->delete();
            }
        }
        Acl::clearCache(User::find($idUsuario));
    }


    /**
     * Comprueba si el equipo no dispone de coordinadores, en tal caso asigna el miembro más antiguo como tal
     */
    public function asignarCoordinador($idUsuarioExcluir = 0)
    {
        // si no quedan coordinadores, hemos de asignar alguno de entre los miembros del equipo, siendo los candidatos los más antiguos
        if (!$this->coordinadores()->count()) {
            $miembroMasAntiguo = $this->miembros()
                ->where('users.id', '!=', $idUsuarioExcluir) // filtramos los miembros con id distinto al usuario que se acaba de modificar
                ->oldest('equipo_user.created_at') // ordenamos los miembros por fecha de membresia
                ->first(); // obtenemos el primer miembro de la lista, que será el más antiguo

            if ($miembroMasAntiguo) {
                $nuevoCoordinador = $miembroMasAntiguo;
                // Actualizamos el rol del usuario en el equipo
                $this->miembros()->updateExistingPivot($nuevoCoordinador->id, ['rol' => 'coordinador']);
                // le damos permisos
                $this->otorgarPermisosCarpetas($nuevoCoordinador->id);
                return $nuevoCoordinador->id;
            }
        }
    }

    /**
     * Crea la carpeta del equipo
     * @throws \Error
     * @return void
     */
    public function crearCarpeta()
    {

        if (!$this->slug) throw new \Error("Falta slug del equipo");

        // Ruta de la carpeta en el sistema de archivos
        $carpetaEquipo = '/archivos/equipos/' . $this->slug;

        // obtenemos el id del usuario propietario, debería ser el propietario del equipo
        $id_user = 1; // admin   //$equipo->user_id ?? auth()->id() ?? 1;

        // Obtén el valor de umask desde el archivo de configuración
        $umask = config('app.umask');

        // Convertir umask y permisos a octal
        $umask = octdec($umask);

        // aplicamos el umask, con el sticky bit 1
        $permisos = 01777 & ~$umask;

        // especifica los permisos de la carpeta
        NodoCarpeta::create([
            'ubicacion' => $carpetaEquipo,
            'user_id' => $id_user,
            'group_id' => $this->group_id,
            'permisos' => decoct($permisos) // convertimos a representación decimal
        ]);

        // Crea la carpeta en el disco público utilizando la clase Storage
        // Storage::disk('archivos')->makeDirectory($carpetaEquipo);
        $loc = new StorageItem($carpetaEquipo);
        $loc->makeDirectory();
    }



    // ACCESOR

    public function getMiembrosJSONAttribute()
    {
        $users = $this->miembros()->select('users.id', 'users.name', 'users.email')->get();
        $usersWithoutPivot = $users->map(function ($user) {
            return [
                'value' => $user->id,
                'label' => $user->name //"{$user->name} <{$user->email}>"
            ];
        });
        return $usersWithoutPivot->toJson();
    }

    public function getCoordinadoresJSONAttribute()
    {
        $users = $this->coordinadores()->select('users.id', 'users.name', 'users.email')->get();
        $usersWithoutPivot = $users->map(function ($user) {
            return [
                'value' => $user->id,
                'label' => $user->name // "{$user->name} <{$user->email}>"
            ];
        });
        return $usersWithoutPivot->toJson();
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



class MembresiaObserver
{
    public function created(Membresia $membresia): void
    {
        Log::info("MembresiaObserver: created", $membresia->toArray());
        // obtenemos el equipo
        $equipo = Equipo::findOrFail($membresia->equipo_id);

        // lo agregamos al grupo
        $grupo=Grupo::findOrFail($equipo->group_id);
        $grupo->usuarios()->attach($membresia->user_id);

        if ($membresia->rol == 'coordinador')
            $equipo->otorgarPermisosCarpetas($membresia->user_id);
    }

    public function updated(Membresia $membresia): void
    {
        Log::info("MembresiaObserver: updated", $membresia->toArray());
        // habrá cambiado el rol u otros atributos de la relación.
        // obtenemos el equipo
        $equipo = Equipo::findOrFail($membresia->equipo_id);
        if ($membresia->rol == 'coordinador') {
            $equipo->otorgarPermisosCarpetas($membresia->user_id);
        } else {
            $equipo->removerPermisosCarpetas($membresia->user_id);
        }
    }

    public function deleted(Membresia $membresia): void
    {
        Log::info("MembresiaObserver: deleted", $membresia->toArray());
        // obtenemos el equipo
        $equipo = Equipo::findOrFail($membresia->equipo_id);

        // lo removemos del grupo
        $grupo=Grupo::findOrFail($equipo->group_id);
        $grupo->usuarios()->detach($membresia->user_id);

        $equipo->removerPermisosCarpetas($membresia->user_id);
    }
}
