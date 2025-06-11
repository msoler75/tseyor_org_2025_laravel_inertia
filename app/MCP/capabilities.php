<?php
// MCP/capabilities.php

return [
    [
        'name' => 'comunicados',
        'description' => 'Herramientas MCP para el modelo Comunicado',
        'tools' => [
            [
                'name' => 'listar_comunicados',
                'description' => 'Lista todos los comunicados.',
                'parameters' => []
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
                'description' => 'Crea un nuevo comunicado.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_comunicado" para detalles de los campos y formatos.'
                    ]
                ]
            ],
            [
                'name' => 'actualizar_comunicado',
                'description' => 'Actualiza un comunicado existente.',
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
                    ]
                ]
            ],
            [
                'name' => 'eliminar_comunicado',
                'description' => 'Elimina un comunicado por su slug.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único del comunicado.'
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
                'description' => 'Lista todas las entradas del blog.',
                'parameters' => []
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
                'description' => 'Crea una nueva entrada.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_entrada" para detalles de los campos y formatos.'
                    ]
                ]
            ],
            [
                'name' => 'actualizar_entrada',
                'description' => 'Actualiza una entrada existente.',
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
                'description' => 'Lista todas las noticias.',
                'parameters' => []
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
                'description' => 'Crea una nueva noticia.',
                'parameters' => [
                    [
                        'name' => 'request',
                        'type' => 'object',
                        'required' => true,
                        'description' => 'Ver tool "campos_noticia" para detalles de los campos y formatos.'
                    ]
                ]
            ],
            [
                'name' => 'actualizar_noticia',
                'description' => 'Actualiza una noticia existente.',
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
                    ]
                ]
            ],
            [
                'name' => 'eliminar_noticia',
                'description' => 'Elimina una noticia por su slug.',
                'parameters' => [
                    [
                        'name' => 'slug',
                        'type' => 'string',
                        'required' => true,
                        'description' => 'Slug único de la noticia.'
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
                'name' => 'campos_comunes',
                'description' => 'Lista los campos comunes (visibilidad, texto, imagen, fecha, etc) y su formato/valores posibles.',
                'parameters' => []
            ],
            [
                'name' => 'campos_comunicado',
                'description' => 'Lista los campos y formatos específicos del modelo Comunicado.',
                'parameters' => []
            ],
            [
                'name' => 'campos_entrada',
                'description' => 'Lista los campos y formatos específicos del modelo Entrada.',
                'parameters' => []
            ],
            [
                'name' => 'campos_noticia',
                'description' => 'Lista los campos y formatos específicos del modelo Noticia.',
                'parameters' => []
            ]
        ]
    ],
];
