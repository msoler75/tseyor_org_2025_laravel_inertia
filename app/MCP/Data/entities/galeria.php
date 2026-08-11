<?php

return [
    'galeria' => [
        'descripcion' => 'Galerías de imágenes y colecciones visuales',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las galerías. Ejemplo: "Convivencias"',
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes"',
            ],
        ],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título de la galería'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'ruta' => ['type' => 'string', 'description' => 'Ruta del directorio de imágenes'],
            'imagen' => ['type' => 'string', 'description' => 'Imagen principal de la galería'],
            'imagen_principal' => ['type' => 'string', 'description' => 'Primera imagen de los items (virtual)'],
        ],
    ],
];
