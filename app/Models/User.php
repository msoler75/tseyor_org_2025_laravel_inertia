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
use App\Models\Invitacion;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Scout\Searchable;
use Illuminate\Support\Facades\Cache;
use App\Notifications\CambioNombreUsuario;
use Carbon\Carbon;

class User extends Authenticatable implements MustVerifyEmail
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

    protected static $creatingUser = false;

    // cuando se crea un usuario, llenaremos el campo "frase" con una frase aleatoria desde un archivo de texto
    public static function boot()
    {
        parent::boot();

        static::saved(function ($user) {

            if (!static::$creatingUser && $user->wasChanged('name')) {
                // El campo 'name' ha cambiado
                // Aquí puedes realizar las acciones que necesites
                // evitaremos si solo han cambiado mayusculas o minúsculas
                $originalName = $user->getOriginal('name');
                $newName = $user->name;
                if(strtolower($originalName) != strtolower($newName))
                    $user->notify(new CambioNombreUsuario());
            }

            // rellena la frase, si está está vacía
            if (trim($user->frase) == "") $user->generarFrase();
        });

        static::created(function ($user) {
            static::$creatingUser = true; // para evitar activaciones del evento saved

            // usuario nuevo, hemos de verificar si aceptó alguna invitación a equipo
            $invitaciones =  Invitacion::where('email', $user->email)
                ->whereNotIn('estado', ['caducada', 'fallida', 'declinada'])
                ->whereNotNull('accepted_at')->get();

            \Log::info("USUARIO CREADO: invitaciones:", ["invitaciones"=>$invitaciones]);

            // recorrer todas las invitaciones, y para cada una, incluimos al usuario en el equipo
            foreach ($invitaciones as $invitacion) {
                // incluimos el usuario al equipo
                $user->equipos()->attach($invitacion->equipo_id);
                // marcamos su email como verificado
                $user->markEmailAsVerified();
                // actualizamos el estado de la invitación
                $invitacion->update(['estado' => 'aceptada']);
            }
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

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'grupo_user', 'user_id', 'group_id')
            ->using(Pertenencia::class)
            ->withPivot(['user_id']);
    }

    // retorna true si este usuario está en el grupo con grupo_id
    public function enGrupo($grupo_id)
    {
        $user = $this;

        // sin cache
        $r =  $user->grupos()->where('grupos.id', $grupo_id)->count();
        \Log::info("user {$user->id} in grupo {$grupo_id} = {$r}");
        return $r >= 1;

        // con cache
        $cacheKey = 'user_grupos_in_' . $this->id . '_group_' . $grupo_id;
        $cacheTime = 30;
        return Cache::remember($cacheKey, $cacheTime, function () use ($user, $grupo_id) {
            return $user->grupos()->where('grupos.id', $grupo_id)->exists();
        });
    }

    /**
     * Obtiene el Access Control List, es una colección de permisos a ciertos nodos, para este usuario
     */
    public function accessControlList()
    {
        return Acl::from($this);
    }

    /**
     * Genera una frase corta
     */
    public function generarFrase()
    {
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
        } while ($loops-- > 0 && User::where('frase', $frase)->count() > 0);
        $this->frase = $frase;
        $this->saveQuietly();
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
