<?php

return [
    'guia' => [
        'descripcion' => 'Guías Estelares de Tseyor. Nuestros tutores del espacio',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las guías. Ejemplo: "Instalación"'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de guía. Ejemplo: "Técnica"'
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
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes"'
            ]
        ],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre de la guía'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría de la guía'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'bibliografia' => ['type' => 'string', 'description' => 'Bibliografía asociada'],
            'libros' => ['type' => 'string', 'description' => 'Libros asociados'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
];
