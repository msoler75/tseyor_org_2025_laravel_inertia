<?php

namespace App\Policies;

use App\Models\Experiencia;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExperienciaPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Experiencia $experiencia): bool
    {
        if ($experiencia->categoria == Experiencia::$CATEGORIA_INTERIORIZACION)
            return $user && $user->esIniciado();
        return true;
    }
}
