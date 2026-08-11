<?php

return [
    'libro' => [
        'descripcion' => 'Libros y lecturas recomendadas',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los libros. Ejemplo: "Cien años de soledad"',
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de libro. Ejemplo: "Novela"',
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
            'titulo' => ['type' => 'string', 'description' => 'Título del libro'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del libro'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen de portada'],
            'imagen_lqip' => ['type' => 'string', 'description' => 'Placeholder LQIP en base64 para carga progresiva de la portada'],
            'edicion' => ['type' => 'string', 'description' => 'Edición del libro'],
            'paginas' => ['type' => 'int', 'description' => 'Número de páginas'],
            'pdf' => ['type' => 'string', 'description' => 'Ruta o URL del PDF'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
        ],
    ],
];
