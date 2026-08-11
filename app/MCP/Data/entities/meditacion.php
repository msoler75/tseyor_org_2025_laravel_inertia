<?php

return [
    'meditacion' => [
        'descripcion' => 'Meditaciones guiadas',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las meditaciones. Ejemplo: "Estrés"',
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de meditación. Ejemplo: "Relajación"',
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023',
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes"',
            ],
        ],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título de la meditación'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría de la meditación'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'audios' => ['type' => 'array', 'description' => 'Lista de archivos de audio asociados'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
        ],
    ],
];
