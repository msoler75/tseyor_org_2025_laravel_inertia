<?php

return [
    'psicografia' => [
        'descripcion' => 'Psicografías y dibujos canalizados',
        'parametros_listar' => [],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título de la psicografía'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen']
        ]
    ],
];
