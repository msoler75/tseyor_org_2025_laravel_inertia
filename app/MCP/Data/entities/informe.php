<?php

return [
    'informe' => [
        'descripcion' => 'Informes de los equipos: Actas, orden del día, resumenes, y otros informes y reportes generados',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los informes. Ejemplo: "Finanzas"'
            ],
            [
                'name' => 'equipo',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar informes por ID o slug de equipo. Ejemplo: 2'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de informe. Ejemplo: "Anual"'
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
            'titulo' => ['type' => 'string', 'description' => 'Título del informe'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del informe'],
            'equipo_id' => ['type' => 'int', 'description' => 'ID del equipo asociado'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'audios' => ['type' => 'array', 'description' => 'Lista de archivos de audio asociados'],
            'archivos' => ['type' => 'array', 'description' => 'Lista de archivos adjuntos'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
];
