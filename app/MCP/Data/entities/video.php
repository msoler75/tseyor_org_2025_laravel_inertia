<?php

return [
    'video' => [
        'descripcion' => 'Videos y grabaciones',
        'parametros_listar' => [],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título del video'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'enlace' => ['type' => 'string', 'description' => 'Enlace al video'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
        ],
    ],
];
