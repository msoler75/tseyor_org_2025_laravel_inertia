<?php

return [
    'experiencia' => [
        'descripcion' => 'Experiencias compartidas por los miembros: sueños, extrapolaciones, rescates, encuentros, psicografías y vivencias personales',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las experiencias. Ejemplo: "onírica"'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría: "Sueños", "Extrapolaciones", "Seiph", "Experiencia de campo", "Rescate adimensional", "Encuentros vis a vis", "Cartas", "Psicografías", "Interiorización", "Otras experiencias".'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes"'
            ]
        ],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre de la experiencia'],
            'fecha' => ['type' => 'string', 'description' => 'Fecha de la experiencia'],
            'lugar' => ['type' => 'string', 'description' => 'Lugar donde ocurrió'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría: Sueños, Extrapolaciones, Seiph, Experiencia de campo, Rescate adimensional, Encuentros vis a vis, Cartas, Psicografías, Interiorización, Otras experiencias'],
            'texto' => ['type' => 'string', 'description' => 'Contenido de la experiencia'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
            'archivo' => ['type' => 'string', 'description' => 'Archivo adjunto (ruta)'],
            'user_id' => ['type' => 'int', 'description' => 'ID del usuario creador']
        ]
    ],
];
