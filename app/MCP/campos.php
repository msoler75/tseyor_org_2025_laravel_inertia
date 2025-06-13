<?php
// MCP/campos.php

return [
    'comunicado' => [
        'titulo' => ['type' => 'string', 'description' => 'Título del comunicado'],
        'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
        'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
        'fecha_comunicado' => ['type' => 'string', 'description' => 'Fecha (YYYY-MM-DD)'],
        'categoria' => ['type' => 'int', 'description' => 'Categoría numérica (0=General, 1=TAP, 2=12 del M., 3=Muul)'],
        'ano' => ['type' => 'int', 'description' => 'Año'],
        'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
        'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
    ],
    'entrada' => [
        'titulo' => ['type' => 'string', 'description' => 'Título de la entrada'],
        'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
        'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
        'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
        'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
        'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
    ],
    'noticia' => [
        'titulo' => ['type' => 'string', 'description' => 'Título de la noticia'],
        'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
        'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
        'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
        'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
        'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
    ],
];
