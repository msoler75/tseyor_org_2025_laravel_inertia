<?php

return [
    'pagina' => [
        'descripcion' => 'Páginas estáticas del sitio',
        'parametros_listar' => [],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título de la página'],
            'ruta' => ['type' => 'string', 'description' => 'Ruta de la página'],
            'atras_ruta' => ['type' => 'string', 'description' => 'Ruta de retroceso'],
            'atras_texto' => ['type' => 'string', 'description' => 'Texto de retroceso'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'palabras_clave' => ['type' => 'string', 'description' => 'Palabras clave SEO'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
];
