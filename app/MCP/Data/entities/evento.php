<?php

return [
    'evento' => [
        'descripcion' => 'Eventos y actividades programadas',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los eventos. Ejemplo: "Concierto"'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de evento. Ejemplo: "Cultura"'
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
            'titulo' => ['type' => 'string', 'description' => 'Título del evento'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del evento'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
            'fecha_inicio' => ['type' => 'string', 'description' => 'Fecha de inicio (YYYY-MM-DD)'],
            'fecha_fin' => ['type' => 'string', 'description' => 'Fecha de fin (YYYY-MM-DD)'],
            'hora_inicio' => ['type' => 'string', 'description' => 'Hora de inicio (HH:MM)'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
            'centro_id' => ['type' => 'int', 'description' => 'ID del centro asociado'],
            'sala_id' => ['type' => 'int', 'description' => 'ID de la sala asociada'],
            'equipo_id' => ['type' => 'int', 'description' => 'ID del equipo organizador']
        ]
    ],
];
