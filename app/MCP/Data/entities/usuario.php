<?php

return [
    'usuario' => [
        'descripcion' => 'Usuarios registrados en la plataforma',
        'parametros_listar' => [
            ['name' => 'buscar', 'type' => 'string', 'required' => false, 'description' => 'Buscar por nombre o email'],
            ['name' => 'equipo_id', 'type' => 'integer', 'required' => false, 'description' => 'Filtrar por equipo'],
            ['name' => 'rol', 'type' => 'string', 'required' => false, 'description' => 'Filtrar por rol'],
        ],
        'campos' => [
            'name' => ['type' => 'string', 'description' => 'Nombre del usuario'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'email' => ['type' => 'string', 'description' => 'Correo electrónico'],
            'frase' => ['type' => 'string', 'description' => 'Frase de perfil'],
            'profile_photo_path' => ['type' => 'string', 'description' => 'Ruta de la foto de perfil'],
            'roles' => ['type' => 'array', 'description' => 'Roles asignados al usuario'],
            'equipos' => ['type' => 'array', 'description' => 'Equipos a los que pertenece'],
            'created_at' => ['type' => 'string', 'description' => 'Fecha de creación'],
            'updated_at' => ['type' => 'string', 'description' => 'Fecha de última actualización'],
        ],
    ],
];
