<?php

return [
    'sala' => [
        'descripcion' => 'Salas virtuales y físicas para eventos',
        'parametros_listar' => [],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre de la sala'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'enlace' => ['type' => 'string', 'description' => 'Enlace de acceso']
        ]
    ],
];
