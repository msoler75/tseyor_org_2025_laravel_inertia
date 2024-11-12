<?php

namespace App\Policies;

use App\Models\Experiencia;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExperienciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Experiencia $experiencia): bool
    {
        if ($experiencia->categoria == Experiencia::$CATEGORIA_INTERIORIZACION)
            return $user->esIniciado();
        return true;
    }
}
