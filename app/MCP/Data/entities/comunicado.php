<?php

return [
    'comunicado' => [
        'descripcion' => 'Comunicados dados por los hermanos mayores o amigos del espacio',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los comunicados. Ejemplo: "Andrómeda"',
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría numérica. Ejemplo: 2',
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2025',
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes"',
            ],
            [
                'name' => 'completo',
                'type' => 'boolean',
                'required' => false,
                'description' => 'Si este campo está a 1 se puede utilizar junto al parámetro "buscar" (usando la tool "buscar" o "listar") y devuelve todas las coincidencias de la palabra o frase de búsqueda',
            ],
        ],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título del comunicado'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'fecha_comunicado' => ['type' => 'string', 'description' => 'Fecha (YYYY-MM-DD)'],
            'categoria' => ['type' => 'int', 'description' => 'Categoría numérica (0=General, 1=TAP, 2=12 del M., 3=Muul)'],
            'ano' => ['type' => 'int', 'description' => 'Año'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
        ],
    ],
];
