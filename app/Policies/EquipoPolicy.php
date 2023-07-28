<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Equipo;

class EquipoPolicy
{
    public function esCoordinador(?User $user, Equipo $equipo) :bool
    {
        return $equipo->esCoordinador(optional($user)->id);
    }
}
