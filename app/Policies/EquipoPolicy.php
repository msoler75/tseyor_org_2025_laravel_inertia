<?php

use App\Models\Equipo;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EquipoPolicy
{
    use HandlesAuthorization;


    public function esCoordinador(User $user, Equipo $equipo)
    {
        return $equipo->esCoordinador($user->id);
    }
}

