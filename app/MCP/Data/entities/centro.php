<?php

return [
    'centro' => [
        'descripcion' => 'Centros Tseyor: Casas Tseyor, Muulasterios y Pueblos Tseyor',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los centros. Ejemplo: "Andalucía"',
            ],
            [
                'name' => 'pais',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por país. Ejemplo: "ES"',
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
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "cronologico"',
            ],
        ],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del centro'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'entradas' => ['type' => 'int', 'description' => 'Número de entradas asociadas'],
            'libros' => ['type' => 'int', 'description' => 'Número de libros asociados'],
            'poblacion' => ['type' => 'string', 'description' => 'Población'],
            'pais' => ['type' => 'string', 'description' => 'Código de país'],
            'contacto_id' => ['type' => 'int', 'description' => 'ID del contacto asociado'],
        ],
    ],
];
