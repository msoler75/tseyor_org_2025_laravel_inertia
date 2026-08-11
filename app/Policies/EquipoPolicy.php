<?php

namespace App\Policies;

use App\Models\Equipo;
use App\Models\User;

class EquipoPolicy
{
    public function esCoordinador(?User $user, Equipo $equipo): bool
    {
        if (! $user) {
            return false;
        }

        return $equipo->esCoordinador($user->id);
    }

    public function esMiembro(?User $user, Equipo $equipo): bool
    {
        if (! $user) {
            return false;
        }

        return $equipo->esMiembro($user->id);
    }
}
