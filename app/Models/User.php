<?php

namespace App\Models;

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

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    public function grupos()
    {
        return Cache::remember("user_grupos_" . $this->id, 30, function () {
            return $this->belongsToMany(Grupo::class, 'grupo_user', 'user_id', 'group_id')
                ->using(Pertenencia::class)
                ->withPivot(['user_id'])
                ->get();
        });
    }
}
