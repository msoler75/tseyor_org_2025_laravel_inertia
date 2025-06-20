<?php
// MCP/capabilities.php

/**
 * Definición de términos clave para el sistema MCP:
 *
 * - Colección: Es un conjunto de elementos del mismo tipo gestionados por el sistema, por ejemplo: libros, comunicados, audios, equipos, etc. Cada colección representa una tabla o recurso principal.
 * - Entidad: Es un tipo de elemento individual dentro de una colección. Por ejemplo, en la colección "libros", cada libro es una entidad. El término "entidad" se usa para referirse tanto al tipo ("libro", "comunicado", etc.) como al identificador concreto de un elemento.
 *
 * En los parámetros de las tools, "entidad" suele referirse al nombre de la colección sobre la que se opera (por ejemplo: "libro", "comunicado").
 */

return [
    [
        'name' => 'ver',
        'description' => 'Obtener los datos de un elemento (entidad) específico de una colección (por ejemplo: "libro", "comunicado", "audio", "equipo", etc.). Una colección es un conjunto de entidades del mismo tipo gestionadas por el sistema. Se debe indicar el nombre de la entidad (tipo de colección) y el id o slug del elemento concreto.',
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
            ],
            [
                'name' => 'token',
                'type' => 'string',
                'description' => 'Token MCP para autenticación o autorización. Opcional salvo que la entidad requiera permisos.',
                'required' => false,
            ]
        ]
    ],
    [
        'name' => 'listar',
        'description' => 'Listar elementos de una colección (por ejemplo: "libros", "comunicados", "audios", etc.). El resultado estará paginado, incluyendo información como página actual, siguiente página, total de páginas y total de elementos. El parámetro "page" permite solicitar la página deseada de resultados. Permite aplicar filtros para limitar los resultados. Para conocer los filtros disponibles, consulta la tool "info" indicando la entidad.',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad a obtener. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ],
            [
                'name' => 'num_pagina',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 2.'
            ],
            [
                'name' => 'token',
                'type' => 'string',
                'description' => 'Token MCP para autenticación o autorización. Opcional salvo que la entidad requiera permisos.',
                'required' => false,
            ]
        ]
    ],
    [
        'name' => 'buscar',
        'description' => 'Buscar cualquier contenido dentro de una colección mediante una frase o palabra clave, usando el parámetro "buscar". Ejemplo: {"entidad": "libro", "buscar": "palabra clave"}. El resultado está paginado igual que en "listar".',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad donde buscar. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ],
            [
                'name' => 'buscar',
                'type' => 'string',
                'description' => 'Frase o palabra clave a buscar en la colección.',
                'required' => true,
            ],
                       [
                'name' => 'num_pagina',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 2.'
            ],
            [
                'name' => 'token',
                'type' => 'string',
                'description' => 'Token MCP para autenticación o autorización. Opcional salvo que la entidad requiera permisos.',
                'required' => false,
            ]
        ]
    ],
    [
        'name' => 'crear',
        'description' => 'Crear un nuevo elemento (entidad) en una colección. Una colección es un conjunto de entidades del mismo tipo gestionadas por el sistema (por ejemplo: "libro", "comunicado", "audio", "equipo", etc.). Se debe indicar el nombre de la entidad (tipo de colección) y los datos a crear. En algunas entidades se requieren campos adicionales de la tool, consulta el valor de "parametros_crear" con la tool "info" indicando la entidad.',
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
                'description' => 'Token MCP para autenticación o autorización.',
                'required' => false,
            ]
        ]
    ],
    [
        'name' => 'editar',
        'description' => 'Editar un elemento (entidad) existente en una colección (por ejemplo: "libro", "comunicado", "audio", "equipo", etc.). Debes indicar la entidad, el id o slug del elemento y los datos a modificar. Ejemplo: {"entidad": "libro", "id": "123", "data": {"titulo": "Nuevo título"}}. En algunas entidades se requieren campos adicionales, consulta el valor de "parametros_crear" o "parametros_editar" con la tool "info" indicando la entidad.',
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
                'description' => 'Token MCP para autenticación o autorización.',
                'required' => true,
            ]
        ]
    ],
    [
        'name' => 'eliminar',
        'description' => 'Eliminar un elemento (entidad) de una colección (por ejemplo: "libro", "comunicado", "audio", "equipo", etc.). Indica la entidad y el id o slug del elemento. Ejemplo: {"entidad": "libro", "id": "123"}. Usa "force": true para eliminar definitivamente.',
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
                'description' => 'Token MCP para autenticación o autorización.',
                'required' => true,
            ]
        ]
    ],
    [
        'name' => 'info',
        'description' => 'Lista entidades (tipos de contenido) y proporciona información sobre una entidad, sus campos y formato. También muestra los filtros disponibles para la tool "listar" o "buscar". Si no se indica entidad, devuelve la lista de entidades. Ejemplos: {} sin parámetros para devolver la lista de entidades; {"entidad": "libro"} para ver la información de la entidad "libro".',
        'parameters' => [
            [
                'name' => 'entidad',
                'type' => 'string',
                'description' => 'El nombre de la entidad de la se solicita información. Por ejemplo: "libro", "comunicado", "audio", "equipo", etc.',
                'required' => true,
            ]
        ]
    ]
];
