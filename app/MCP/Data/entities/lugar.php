<?php

return [
    'lugar' => [
        'descripcion' => 'Lugares de interés en la Galaxia',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los lugares. Ejemplo: "Parque"'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de lugar. Ejemplo: "Parques"'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia"'
            ]
        ],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del lugar'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del lugar'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'libros' => ['type' => 'string', 'description' => 'Libros asociados'],
            'relacionados' => ['type' => 'string', 'description' => 'Lugares relacionados'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
];
