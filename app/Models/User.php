<?php

namespace App\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Permiso;
use App\Models\Equipo;
use App\Models\Grupo;
use App\Models\Membresia;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use CrudTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Illuminate\Database\Eloquent\SoftDeletes;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;
    use Searchable;

    protected $revisionCreationsEnabled = true;

    // cuando se crea un usuario, llenaremos el campo "frase" con una frase aleatoria desde un archivo de texto
    public static function boot() {
        parent::boot();

        static::saved(function ($user) {
            // rellena la frase, si está está vacía
            if(trim($user->frase)!="") return;

            // archivo está en @/resources/txt/frases_cortas.txt
            $file = base_path('resources/txt/frases_cortas.txt');
            $content = file_get_contents($file);
            $frases = preg_split("/\r\n|\n|\r/", $content, -1, PREG_SPLIT_NO_EMPTY);
            // mezclamos el orden de las frases
            shuffle($frases);
            $loops = count($frases);
            do {
                $frase = array_pop($frases);
                // comprobamos que la frase no la tiene otro usuario
            } while($loops-- > 0 && User::where('frase', $frase)->count() > 0);
            $user->frase = $frase;
            $user->save();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'email',
        'password',
        'frase',
        'profile_photo_path'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public function contacto() // contacto relacionado con este usuario
    {
        return $this->belongsTo(Contacto::class, 'contacto_id');
    }

    public function permisos()
    {
        return $this->hasMany(Permiso::class);
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_user')
            ->using(Membresia::class)
            ->withPivot(['user_id', 'rol']);
    }

    public function equiposQueCoordina()
    {
        // segun la tabla pivot, el rol es 'coordinador'
        // Retrieve the teams that this user coordinates based on the pivot table where the role is 'coordinador'
        return $this->belongsToMany(Equipo::class, 'equipo_user')
            ->using(Membresia::class)
            ->wherePivot('rol', 'coordinador')
            ->withPivot(['user_id', 'rol']);
    }

    /*public function grupos()
    {
        return Cache::remember("user_grupos_" . $this->id, 30, function () {
            return $this->belongsToMany(Grupo::class, 'grupo_user', 'user_id', 'group_id')
                ->using(Pertenencia::class)
                ->withPivot(['user_id'])
                ->get();
        });
    }*/

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_user', 'user_id', 'group_id')
            ->using(Pertenencia::class)
            ->withPivot(['user_id']);
    }

    /**
     * Obtiene el Access Control List, es una colección de permisos a ciertos nodos, para este usuario
     */
    public function accessControlList()
    {
        return Acl::from($this);
    }


    // ACCESOR

    public function getGruposJSONAttribute()
    {
        $grupos = $this->grupos()->select('grupos.id', 'grupos.nombre')->get();
        $gruposWithoutPivot = $grupos->map(function ($grupo) {
            return [
                'value' => $grupo->id,
                'label' => $grupo->nombre,
            ];
        });
        return $gruposWithoutPivot->toJson();
    }


    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            // <- Always include the primary key
            'nombre' => $this->name,
        ];
    }
}
