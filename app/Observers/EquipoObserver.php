<?php

namespace App\Observers;

use App\Models\Equipo;
use App\Models\Grupo;
use App\Models\NodoCarpeta;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use App\Pigmalion\StorageItem;

/**
 * Cada equipo tiene un grupo-espejo que tiene el mismo nombre.
 * Esto se hace así para poder manejar permisos de carpetas y archivos con usuarios y grupos.
 */
class EquipoObserver
{
    /**
     * Handle the Equipo "created" event.
     *
     * @param  \App\Models\Equipo  $equipo
     * @return void
     */
    public function created(Equipo $equipo)
    {
        \Log::info("EquipoObserver.created");

        $slug = $equipo->slug ?? Str::slug($equipo->nombre);
        if (!$equipo->slug)
            $equipo->slug = $slug;

        // Crea un nuevo grupo con el mismo nombre del equipo
        $grupo = Grupo::create(['nombre' => $equipo->nombre, 'slug' => $slug]);

        $equipo->group_id = $grupo->id;
        $equipo->save();

        // Ruta de la carpeta en el sistema de archivos
        $carpetaEquipo = '/archivos/equipos/' . $slug;

        // obtenemos el id del usuario propietario, debería ser el propietario del equipo
        $id_user = 1; // admin   //$equipo->user_id ?? auth()->id() ?? 1;

        // Obtén el valor de umask desde el archivo de configuración
        $umask = Config::get('app.umask');

        // Convertir umask y permisos a octal
        $umask = octdec($umask);

        // aplicamos el umask, con el sticky bit 1
        $permisos = 01777 & ~$umask;

        // especifica los permisos de la carpeta
        NodoCarpeta::create([
            'ubicacion' => $carpetaEquipo,
            'user_id' => $id_user,
            'group_id' => $grupo->id,
            'permisos' => decoct($permisos) // convertimos a representación decimal
        ]);

         // Crea la carpeta en el disco público utilizando la clase Storage
         // Storage::disk('archivos')->makeDirectory($carpetaEquipo);
         $loc = new StorageItem($carpetaEquipo);
         $loc->makeDirectory();
    }


    /**
     * detectamos cambio en el propietario y cambiamos el propietario del grupo espejo
     */
    public function updated(Equipo $equipo)
    {
        // los cambios en la carpeta se tienen que hacer en el explorador de archivos o el administrador
    }
}
