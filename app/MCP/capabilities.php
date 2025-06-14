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
    [
        'name' => 'audios',
        'description' => 'Herramientas MCP para el modelo Audio',
        'tools' => [
            [
                'name' => 'listar_audios',
                'description' => 'Lista todos los audios. Permite buscar por texto en título o descripción y filtrar por categoría.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en título o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ]
                ]
            ],
            [
                'name' => 'ver_audio',
                'description' => 'Obtiene un audio por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del audio. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del audio. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_audio',
                'description' => 'Crea un nuevo audio. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_audio" para detalles de los campos y formatos.'
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
                'name' => 'editar_audio',
                'description' => 'Actualiza un audio existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del audio. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del audio. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_audio" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_audio',
                'description' => 'Elimina un audio por su id o slug. Permite borrado lógico (soft delete) o definitivo (hard delete). Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del audio. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del audio. Alternativamente se puede usar id.'
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
        'name' => 'centros',
        'description' => 'Herramientas MCP para el modelo Centro',
        'tools' => [
            [
                'name' => 'listar_centros',
                'description' => 'Lista todos los centros. No admite parámetros de filtrado especiales.',
                'parameters' => []
            ],
            [
                'name' => 'ver_centro',
                'description' => 'Obtiene un centro por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del centro. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del centro. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_centro',
                'description' => 'Crea un nuevo centro. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_centro" para detalles de los campos y formatos.'
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
                'name' => 'editar_centro',
                'description' => 'Actualiza un centro existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del centro. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del centro. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_centro" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_centro',
                'description' => 'Elimina un centro por su id o slug. Permite borrado lógico (soft delete) o definitivo (hard delete). Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del centro. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del centro. Alternativamente se puede usar id.'
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
        'name' => 'contactos',
        'description' => 'Herramientas MCP para el modelo Contacto',
        'tools' => [
            [
                'name' => 'listar_contactos',
                'description' => 'Lista todos los contactos. Permite buscar por nombre, población, provincia, etc.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en nombre, población, provincia, etc.'
                    ]
                ]
            ],
            [
                'name' => 'ver_contacto',
                'description' => 'Obtiene un contacto por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del contacto. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del contacto. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_contacto',
                'description' => 'Crea un nuevo contacto. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_contacto" para detalles de los campos y formatos.'
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
                'name' => 'editar_contacto',
                'description' => 'Actualiza un contacto existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del contacto. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del contacto. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_contacto" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_contacto',
                'description' => 'Elimina un contacto por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del contacto. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del contacto. Alternativamente se puede usar id.'
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
        'name' => 'equipos',
        'description' => 'Herramientas MCP para el modelo Equipo',
        'tools' => [
            [
                'name' => 'listar_equipos',
                'description' => 'Lista todos los equipos. Permite buscar por nombre o descripción y filtrar por categoría.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en nombre o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ]
                ]
            ],
            [
                'name' => 'ver_equipo',
                'description' => 'Obtiene un equipo por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del equipo. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del equipo. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_equipo',
                'description' => 'Crea un nuevo equipo. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_equipo" para detalles de los campos y formatos.'
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
                'name' => 'editar_equipo',
                'description' => 'Actualiza un equipo existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del equipo. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del equipo. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_equipo" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_equipo',
                'description' => 'Elimina un equipo por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del equipo. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del equipo. Alternativamente se puede usar id.'
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
        'name' => 'eventos',
        'description' => 'Herramientas MCP para el modelo Evento',
        'tools' => [
            [
                'name' => 'listar_eventos',
                'description' => 'Lista todos los eventos. Permite buscar por título o descripción, filtrar por categoría, fecha de inicio y fecha de fin.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en título o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ],
                    [
                        'name' => 'fecha_inicio',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por fecha de inicio (YYYY-MM-DD).'
                    ],
                    [
                        'name' => 'fecha_fin',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por fecha de fin (YYYY-MM-DD).'
                    ]
                ]
            ],
            [
                'name' => 'ver_evento',
                'description' => 'Obtiene un evento por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del evento. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del evento. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_evento',
                'description' => 'Crea un nuevo evento. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_evento" para detalles de los campos y formatos.'
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
                'name' => 'editar_evento',
                'description' => 'Actualiza un evento existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del evento. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del evento. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_evento" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_evento',
                'description' => 'Elimina un evento por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del evento. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del evento. Alternativamente se puede usar id.'
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
        'name' => 'grupos',
        'description' => 'Herramientas MCP para el modelo Grupo',
        'tools' => [
            [
                'name' => 'listar_grupos',
                'description' => 'Lista todos los grupos. Permite buscar por nombre o descripción.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en nombre o descripción.'
                    ]
                ]
            ],
            [
                'name' => 'ver_grupo',
                'description' => 'Obtiene un grupo por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del grupo. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del grupo. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_grupo',
                'description' => 'Crea un nuevo grupo. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_grupo" para detalles de los campos y formatos.'
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
                'name' => 'editar_grupo',
                'description' => 'Actualiza un grupo existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del grupo. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del grupo. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_grupo" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_grupo',
                'description' => 'Elimina un grupo por su id o slug. Permite borrado lógico (soft delete) o definitivo (hard delete). Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del grupo. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del grupo. Alternativamente se puede usar id.'
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
        'name' => 'guias',
        'description' => 'Herramientas MCP para el modelo Guia',
        'tools' => [
            [
                'name' => 'listar_guias',
                'description' => 'Lista todas las guías. Permite buscar por nombre o descripción y filtrar por categoría.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en nombre o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ]
                ]
            ],
            [
                'name' => 'ver_guia',
                'description' => 'Obtiene una guía por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la guía. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la guía. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_guia',
                'description' => 'Crea una nueva guía. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_guia" para detalles de los campos y formatos.'
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
                'name' => 'editar_guia',
                'description' => 'Actualiza una guía existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la guía. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la guía. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_guia" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_guia',
                'description' => 'Elimina una guía por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la guía. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la guía. Alternativamente se puede usar id.'
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
        'name' => 'informes',
        'description' => 'Herramientas MCP para el modelo Informe',
        'tools' => [
            [
                'name' => 'listar_informes',
                'description' => 'Lista todos los informes. Permite buscar por título o descripción y filtrar por categoría.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en título o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ]
                ]
            ],
            [
                'name' => 'ver_informe',
                'description' => 'Obtiene un informe por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del informe. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del informe. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_informe',
                'description' => 'Crea un nuevo informe. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_informe" para detalles de los campos y formatos.'
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
                'name' => 'editar_informe',
                'description' => 'Actualiza un informe existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del informe. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del informe. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_informe" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_informe',
                'description' => 'Elimina un informe por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del informe. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del informe. Alternativamente se puede usar id.'
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
        'name' => 'libros',
        'description' => 'Herramientas MCP para el modelo Libro',
        'tools' => [
            [
                'name' => 'listar_libros',
                'description' => 'Lista todos los libros. Permite buscar por título o descripción y filtrar por categoría.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en título o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ]
                ]
            ],
            [
                'name' => 'ver_libro',
                'description' => 'Obtiene un libro por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del libro. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del libro. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_libro',
                'description' => 'Crea un nuevo libro. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_libro" para detalles de los campos y formatos.'
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
                'name' => 'editar_libro',
                'description' => 'Actualiza un libro existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del libro. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del libro. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_libro" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_libro',
                'description' => 'Elimina un libro por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del libro. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del libro. Alternativamente se puede usar id.'
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
        'name' => 'lugares',
        'description' => 'Herramientas MCP para el modelo Lugar',
        'tools' => [
            [
                'name' => 'listar_lugares',
                'description' => 'Lista todos los lugares. Permite buscar por nombre o descripción y filtrar por categoría.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en nombre o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ]
                ]
            ],
            [
                'name' => 'ver_lugar',
                'description' => 'Obtiene un lugar por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del lugar. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del lugar. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_lugar',
                'description' => 'Crea un nuevo lugar. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_lugar" para detalles de los campos y formatos.'
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
                'name' => 'editar_lugar',
                'description' => 'Actualiza un lugar existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del lugar. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del lugar. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_lugar" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_lugar',
                'description' => 'Elimina un lugar por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID del lugar. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único del lugar. Alternativamente se puede usar id.'
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
        'name' => 'meditaciones',
        'description' => 'Herramientas MCP para el modelo Meditacion',
        'tools' => [
            [
                'name' => 'listar_meditaciones',
                'description' => 'Lista todas las meditaciones. Permite buscar por título o descripción y filtrar por categoría.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en título o descripción.'
                    ],
                    [
                        'name' => 'categoria',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Filtrar por categoría.'
                    ]
                ]
            ],
            [
                'name' => 'ver_meditacion',
                'description' => 'Obtiene una meditación por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la meditación. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la meditación. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_meditacion',
                'description' => 'Crea una nueva meditación. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_meditacion" para detalles de los campos y formatos.'
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
                'name' => 'editar_meditacion',
                'description' => 'Actualiza una meditación existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la meditación. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la meditación. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_meditacion" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_meditacion',
                'description' => 'Elimina una meditación por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la meditación. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la meditación. Alternativamente se puede usar id.'
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
        'name' => 'normativas',
        'description' => 'Herramientas MCP para el modelo Normativa',
        'tools' => [
            [
                'name' => 'listar_normativas',
                'description' => 'Lista todas las normativas. Permite buscar por título o descripción.',
                'parameters' => [
                    [
                        'name' => 'buscar',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Texto a buscar en título o descripción.'
                    ]
                ]
            ],
            [
                'name' => 'ver_normativa',
                'description' => 'Obtiene una normativa por su id o slug.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la normativa. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la normativa. Alternativamente se puede usar id.'
                    ]
                ]
            ],
            [
                'name' => 'crear_normativa',
                'description' => 'Crea una nueva normativa. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_normativa" para detalles de los campos y formatos.'
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
                'name' => 'editar_normativa',
                'description' => 'Actualiza una normativa existente. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la normativa. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la normativa. Alternativamente se puede usar id.'
                    ],
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_normativa" para detalles de los campos y formatos.'
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
                'name' => 'eliminar_normativa',
                'description' => 'Elimina una normativa por su id o slug. Requiere token de autorización.',
                'parameters' => [
                    [
                        'name' => 'id',
                        'type' => 'integer',
                        'required' => false,
                        'description' => 'ID de la normativa. Alternativamente se puede usar slug.'
                    ],
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => false,
                        'description' => 'Slug único de la normativa. Alternativamente se puede usar id.'
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
];

