<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grupo;
use App\Models\Nodo;
use Illuminate\Support\Str;

class BaseTest extends TestCase {

    
    /**
     * Retrieves a user by name. If the user does not exist, creates a new user with the given name, email, slug, and password.
     *
     * @param string $nombre The name of the user to retrieve or create.
     * @return \App\Models\User The retrieved or created user.
     */
    protected function getUser($nombre)
    {
        // $faker = \Faker\Factory::create();
        $user = User::where('name', $nombre)->first();
        if (!$user)
            $user = User::create(['name' => $nombre, 'email' => $nombre . '@gmaix.co', 'slug' => Str::slug($nombre), 'password' => '123456678']);
        return $user;
    }


    protected function getGrupo($nombre)
    {
        // $faker = \Faker\Factory::create();
        $group = Grupo::where('nombre', $nombre)->first();
        if (!$group)
            $group = Grupo::create(['nombre' => $nombre, 'slug' => Str::slug($nombre)]);
        return $group;
    }


    /**
     *
     * Obtiene el nodo de la ubicación especificada, y resetea sus valores
     * @param mixed $ubicacion ubicación
     * @param mixed $values aplica valores
     * @return Nodo|object|\Illuminate\Database\Eloquent\Model
     */
    protected function getNodo($ubicacion, $values = [])
    {
        // $faker = \Faker\Factory::create();
        $nodo = Nodo::where('ubicacion', $ubicacion)->first();
        if (!$nodo)
            $nodo = Nodo::create(['ubicacion' => $ubicacion]);
        $reset_values = [
            'user_id' => 1,
            'group_id' => 1,
            'permisos' => '1755',
            'es_carpeta' => 0,
        ];
        $update = array_merge($reset_values, $values);
        // \Log::info("update nodo {$nodo->id}",['values'=>$values, 'update'=>$update]);
        $nodo->update($update);
        return $nodo;
    }



}
