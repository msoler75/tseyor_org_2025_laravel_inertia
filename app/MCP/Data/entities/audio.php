<?php

return [
    'audio' => [
        'descripcion' => 'Audios disponibles para escuchar o descargar',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los audios. Ejemplo: "Meditación"',
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de audio. Ejemplo: "Relajación"',
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
            'titulo' => ['type' => 'string', 'description' => 'Título del audio'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del audio'],
            'enlace' => ['type' => 'string', 'description' => 'Enlace externo (opcional)'],
            'audio' => ['type' => 'string', 'description' => 'Ruta o URL del archivo de audio'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
            'duracion' => ['type' => 'string', 'description' => 'Duración del audio (opcional)'],
        ],
    ],
];
