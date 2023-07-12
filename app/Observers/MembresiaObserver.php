<?php

namespace App\Observers;

use App\Models\Membresia;
use App\Models\Equipo;
use App\Models\User;
use App\Models\Grupo;

class MembresiaObserver
{
    /**
     * Handle the Membresia "created" event.
     */
    public function created(Membresia $membresia): void
    {
        // obtenemos el usuario y nuevo miembro del equipo
        $usuario = User::findOrFail($membresia->user_id);
        // obtenemos el equipo
        $equipo = Equipo::findOrFail($membresia->equipo_id);
        // obtenemos el grupo asociado al equipo
        $grupo = Grupo::findOrFail($equipo->group_id);
        // agregamos el usuario al grupo
        $grupo->usuarios()->syncWithoutDetaching($usuario->id);
    }

    /**
     * Handle the Membresia "updated" event.
     */
    public function updated(Membresia $membresia): void
    {
        // habrá cambiado el rol u otros atributos de la relación.
        // no hacemos nada
    }

    /**
     * Handle the Membresia "deleted" event.
     */
    public function deleted(Membresia $membresia): void
    {
         // obtenemos el usuario y el miembro del equipo que se ha retirado del equipo
         $usuario = User::findOrFail($membresia->user_id);
         // obtenemos el equipo
         $equipo = Equipo::findOrFail($membresia->equipo_id);
         // obtenemos el grupo asociado al equipo
         $grupo = Grupo::findOrFail($equipo->group_id);
         // eliminamos al usuario del grupo
        $grupo->usuarios()->detach($usuario->id);
    }
}
