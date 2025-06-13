<?php
// MCP/capabilities.php

return [
    [
        'name' => 'comunicados',
        'description' => 'Herramientas MCP para el modelo Comunicado',
        'tools' => [
            [
                'name' => 'listar_comunicados',
                'description' => 'Lista todos los comunicados. Permite buscar por texto, filtrar por categoría y año, y ordenar. Soporta paginación.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en los comunicados. Ejemplo: "Andrómeda".'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría numérica. Ejemplo: 2.'
                    ],
                    [
                        'name' => 'ano',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'Filtrar por año. Ejemplo: 2025.'
                    ],
                    [
                        'name' => 'orden',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes".'
                    ],
                    [
                        'name' => 'page',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
                    ]
                ]
            ],
            [
                'name' => 'ver_comunicado',
                'description' => 'Obtiene un comunicado por su slug.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único del comunicado.'
                    ]
                ]
            ],
            [
                'name' => 'crear_comunicado',
                'description' => 'Crea un nuevo comunicado. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_comunicado" para detalles de los campos y formatos. Todos los datos deben ir dentro de este objeto.'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
            [
                'name' => 'editar_comunicado',
                'description' => 'Actualiza un comunicado existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único del comunicado.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_comunicado" para detalles de los campos y formatos.'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
            [
                'name' => 'eliminar_comunicado',
                'description' => 'Elimina un comunicado por su slug o id. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del comunicado. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del comunicado. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'force',
                        'type' => 'boolean',
                        'required' => false,
                        'description' => 'Si es true, elimina definitivamente (hard delete). Por defecto es false (soft delete).'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
        ],
    ],
    [
        'name' => 'entradas',
        'description' => 'Herramientas MCP para el modelo Entrada',
        'tools' => [
            [
                'name' => 'listar_entradas',
                'description' => 'Lista todas las entradas del blog. Permite buscar por texto y soporta paginación.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en las entradas. Ejemplo: "sabios".'
                    ],
                    [
                        'name' => 'page',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
                    ]
                ]
            ],
            [
                'name' => 'ver_entrada',
                'description' => 'Obtiene una entrada por su slug.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único de la entrada.'
                    ]
                ]
            ],
            [
                'name' => 'crear_entrada',
                'description' => 'Crea una nueva entrada. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_entrada" para detalles de los campos y formatos. Todos los datos deben ir dentro de este objeto.'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
            [
                'name' => 'editar_entrada',
                'description' => 'Actualiza una entrada existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único de la entrada.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_entrada" para detalles de los campos y formatos.'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
            [
                'name' => 'eliminar_entrada',
                'description' => 'Elimina una entrada por su slug o id. Por defecto realiza un borrado lógico (soft delete). Si se pasa el parámetro force=true, elimina definitivamente (hard delete). Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la entrada. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la entrada. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'force',
                        'type' => 'boolean',
                        'required' => false,
                        'description' => 'Si es true, elimina definitivamente (hard delete). Por defecto es false (soft delete).'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
        ],
    ],
    [
        'name' => 'noticias',
        'description' => 'Herramientas MCP para el modelo Noticia',
        'tools' => [
            [
                'name' => 'listar_noticias',
                'description' => 'Lista todas las noticias. Permite buscar por texto y soporta paginación.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en las noticias. Ejemplo: "galaxia".'
                    ],
                    [
                        'name' => 'page',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
                    ]
                ]
            ],
            [
                'name' => 'ver_noticia',
                'description' => 'Obtiene una noticia por su slug.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único de la noticia.'
                    ]
                ]
            ],
            [
                'name' => 'crear_noticia',
                'description' => 'Crea una nueva noticia. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_noticia" para detalles de los campos y formatos. Todos los datos deben ir dentro de este objeto.'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
            [
                'name' => 'editar_noticia',
                'description' => 'Actualiza una noticia existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único de la noticia.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_noticia" para detalles de los campos y formatos.'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
            [
                'name' => 'eliminar_noticia',
                'description' => 'Elimina una noticia por su slug o id. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la noticia. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la noticia. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'force',
                        'type' => 'boolean',
                        'required' => false,
                        'description' => 'Si es true, elimina definitivamente (hard delete). Por defecto es false (soft delete).'
                    ],
                    [
                        'name' => 'token',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Token MCP necesario para autorización.'
                    ]
                ]
            ],
        ],
    ],
    [
        'name' => 'info_campos',
        'description' => 'Devuelve información detallada sobre los campos comunes y su formato en los modelos principales.',
        'tools' => [
            [
                'name' => 'campos_comunicado',
                'description' => 'Lista los campos y formatos específicos del modelo Comunicado. Útil para saber qué datos enviar al crear o editar un comunicado.',
                'parameters' => []
            ],
            [
                'name' => 'campos_entrada',
                'description' => 'Lista los campos y formatos específicos del modelo Entrada. Útil para saber qué datos enviar al crear o editar una entrada.',
                'parameters' => []
            ],
            [
                'name' => 'campos_noticia',
                'description' => 'Lista los campos y formatos específicos del modelo Noticia. Útil para saber qué datos enviar al crear o editar una noticia.',
                'parameters' => []
            ]
        ]
    ],
];

