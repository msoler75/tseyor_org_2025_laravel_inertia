<?php
// MCP/capabilities.php

return [
    [
        'name' => 'ver',
        'description' => 'Obtener los datos de un elemento específico de una colección (libro, comunicado, audio, equipo, etc.) Se debe indicar la entidad y especificar el id o slug del elemento.',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad a obtener. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ],
            [
                'name' => 'id',
                'type' => 'string',
                'description' => 'El ID o slug del elemento a obtener. Si se proporciona un ID numérico, se buscará por ID; si es un slug, se buscará por slug.',
                'required' => true,
            ]
        ]
    ],
    [
        'name' => 'listar',
        'description' => 'Obtener una lista de elementos de una colección (libros, comunicados, audios, equipos, etc.) Se debe indicar la entidad. Se puede consultar la tool "info" donde se pueden ver los parámetros opcionales para buscar y filtrar de la entidad cuando se usa la tool "listar"',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad a obtener. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ]
        ]
    ],
    [
        'name' => 'crear',
        'description' => 'Crear un nuevo elemento en una colección. Se debe indicar la entidad y los datos a crear. Se puede consultar la tool "info" para obtener los campos de la entidad.',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad donde crear el elemento. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ],
            [
                'name' => 'data',
                'type' => 'object',
                'description' => 'Datos del nuevo elemento. Ver la tool "campos" para los campos requeridos.',
                'required' => true,
            ],
            [
                'name' => 'token',
                'type' => 'string',
                'description' => 'Token MCP necesario para autorización.',
                'required' => true,
            ]
        ]
    ],
    [
        'name' => 'editar',
        'description' => 'Editar un elemento existente en una colección. Se debe indicar la entidad, el id o slug y los datos a modificar. Se puede consultar la tool "info" para obtener los campos de la entidad.',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad donde editar el elemento. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ],
            [
                'name' => 'id',
                'type' => 'string',
                'description' => 'El ID o slug del elemento a editar.',
                'required' => true,
            ],
            [
                'name' => 'data',
                'type' => 'object',
                'description' => 'Datos a modificar. Ver la tool "campos" para los campos permitidos.',
                'required' => true,
            ],
            [
                'name' => 'token',
                'type' => 'string',
                'description' => 'Token MCP necesario para autorización.',
                'required' => true,
            ]
        ]
    ],
    [
        'name' => 'eliminar',
        'description' => 'Eliminar un elemento de una colección. Se debe indicar la entidad y el id o slug. ',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad donde eliminar el elemento. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ],
            [
                'name' => 'id',
                'type' => 'string',
                'description' => 'El ID o slug del elemento a eliminar.',
                'required' => true,
            ],
            [
                'name' => 'force',
                'type' => 'boolean',
                'description' => 'Si es true, elimina definitivamente (hard delete). Por defecto es false (soft delete).',
                'required' => false,
            ],
            [
                'name' => 'token',
                'type' => 'string',
                'description' => 'Token MCP necesario para autorización.',
                'required' => true,
            ]
        ]
    ],
    [
        'name' => 'info',
        'description' => 'Proporciona información de la entidad, los campos de la entidad, su formato, etc. También detalla parámetros opcionales para buscar y filtrar resultados en la tool "listar". Si no se especifica la entidad, devuelve una lista de entidades disponibles.',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ]
        ]
    ]
];
