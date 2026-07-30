<?php

return [
    'tutorial' => [
        'descripcion' => 'Tutoriales y guías prácticas',
        'parametros_listar' => [],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título del tutorial'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'video' => ['type' => 'string', 'description' => 'Enlace al video'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
];
