<?php

return [
    'publicacion' => [
        'descripcion' => 'Publicaciones y documentos compartidos por los equipos y miembros',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las publicaciones. Ejemplo: "Informe"'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de publicación. Ejemplo: "Documentación"'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes"'
            ]
        ],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título de la publicación'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría'],
            'texto' => ['type' => 'string', 'description' => 'Contenido de la publicación'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
            'user_id' => ['type' => 'int', 'description' => 'ID del autor'],
            'equipo_id' => ['type' => 'int', 'description' => 'ID del equipo asociado']
        ]
    ],
];
