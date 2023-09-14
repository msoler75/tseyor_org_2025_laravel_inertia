<?php

namespace App\Imports;

use App\Models\User;

class UserImport
{

    public static function importar()
    {

        $lista_usuarios = base_path('resources/data/users.json');

        $contenido = file_get_contents($lista_usuarios);
        $usuarios = json_decode($contenido, true);

        foreach ($usuarios as $usuario) {
                $nuevoUsuario = new User();
                $nuevoUsuario->name = $usuario['name'];

                $nuevoUsuario->save();
        }
    }
}
