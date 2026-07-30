<?php

return [
    'equipo' => [
        'descripcion' => 'Equipos de trabajo y colaboración',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los equipos. Ejemplo: "Desarrollo"'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de equipo. Ejemplo: "Proyectos"'
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
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "cronologico"'
            ]
        ],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del equipo'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del equipo'],
            'group_id' => ['type' => 'int', 'description' => 'ID del grupo asociado'],
            'anuncio' => ['type' => 'string', 'description' => 'Anuncio del equipo'],
            'reuniones' => ['type' => 'string', 'description' => 'Información sobre reuniones'],
            'informacion' => ['type' => 'string', 'description' => 'Información adicional'],
            'oculto' => ['type' => 'boolean', 'description' => 'Si el equipo está oculto'],
            'ocultarCarpetas' => ['type' => 'boolean', 'description' => 'Ocultar carpetas asociadas'],
            'ocultarArchivos' => ['type' => 'boolean', 'description' => 'Ocultar archivos asociados'],
            'ocultarMiembros' => ['type' => 'boolean', 'description' => 'Ocultar miembros del equipo'],
            'ocultarSolicitudes' => ['type' => 'boolean', 'description' => 'Ocultar solicitudes de membresía']
        ]
    ],
];
