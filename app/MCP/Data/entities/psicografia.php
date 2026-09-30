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
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'visibilidad' => ['type' => 'string', 'description' => "Estado de publicación: 'P'=Publicado, 'B'=Borrador"],
            'para_puzle' => ['type' => 'boolean', 'description' => 'Si puede lanzarse en el puzzle (puzle.tseyor.org). Por defecto false'],
        ],
    ],
];
