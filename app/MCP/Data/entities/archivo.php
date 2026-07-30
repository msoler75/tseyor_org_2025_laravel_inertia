<?php

return [
    'archivo' => [
        'descripcion' => 'Gestión de archivos y carpetas en el sistema de almacenamiento. Los archivos y carpetas no se acceden por "id" sino por "ruta". Ejemplo: {ruta: "/archivos/hola.txt"}',
        'parametros_ver' => [
            [
                'name' => 'ruta',
                'type' => 'string',
                'description' => 'La ruta del archivo a ver su información',
                'required' => true,
            ],
        ],
        'parametros_listar' => [
            [
                'name' => 'ruta',
                'type' => 'string',
                'required' => false,
                'description' => 'Ruta de la carpeta a listar. Si se omite, se listará la raíz'
            ],
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los nombres de archivos o carpetas'
            ]
        ],
        'ejemplos_crear' => 'Para crear un archivo de texto con permisos 557: {"entidad": "archivo", "ruta": "/archivos/personal/public/conseguido.txt", "data":["contenido": "Texto del archivo", "permisos": "557" ]} o para crear una carpeta: {"entidad": "archivo", "ruta": "/archivos/personal/public/nueva_carpeta/", "data":["es_carpeta": true]}
Para crear una carpeta con permisos específicos: {"entidad": "archivo", "ruta": "/archivos/personal/public/nueva_carpeta/", "data":["permisos": "1775", "group_id": 10]}
Para subir un archivo binario (ejemplo PDF) usando base64: {"entidad": "archivo", "ruta": "/archivos/ejemplo.pdf", "data": {"contenido_base64": "JVBERi0xLjQKJUZha2UgUERG..."}}',
        'parametros_crear' => [
            [
                'name' => 'ruta',
                'type' => 'string',
                'required' => true,
                'description' => 'Ruta completa donde se creará el archivo o carpeta. Ejemplo: /archivos/personal/public/conseguido.txt o /archivos/personal/public/nueva_carpeta/'
            ],
            [
                'name' => 'contenido',
                'type' => 'string',
                'required' => false,
                'description' => 'Contenido del archivo en texto plano. Si se omite, se creará una carpeta'
            ],
            [
                'name' => 'contenido_base64',
                'type' => 'string',
                'required' => false,
                'description' => 'Contenido binario COMPLETO del archivo codificado en base64 (para archivos binarios como PDF, imágenes, etc). Obligatorio para subida binaria vía MCP/JSON-RPC.'
            ],
            [
                'name' => 'es_carpeta',
                'type' => 'boolean',
                'required' => false,
                'description' => 'Si es true, se crea una carpeta aunque se envíe contenido'
            ]
        ],
        'ejemplos_editar' => 'Para renombrar un archivo: {"entidad": "archivo", "ruta": "/archivos/personal/public/conseguido.txt", "nuevo_nombre": "nuevo_nombre.txt"}\nPara cambiar permisos: {"entidad": "archivo", "ruta": "/archivos/personal/public/conseguido.txt", "data": {"permisos": "1775"}}\nPara cambiar propietario: {"entidad": "archivo", "ruta": "/archivos/personal/public/conseguido.txt", "data": {"group_id": 10, "user_id": 5}}',
        'parametros_editar' => [
            [
                'name' => 'ruta',
                'type' => 'string',
                'required' => true,
                'description' => 'Ruta completa del archivo o carpeta. Ejemplo: /archivos/personal/public/conseguido.txt'
            ],
            [
                'name' => 'nuevo_nombre',
                'type' => 'string',
                'required' => false,
                'description' => 'Nuevo nombre para el archivo o carpeta'
            ]
        ],
        'parametros_eliminar' => [
            [
                'name' => 'ruta',
                'type' => 'string',
                'required' => true,
                'description' => 'Ruta completa del archivo o carpeta'
            ]
        ],
        'parametros_buscar' => [
            [
                'name' => 'nombre',
                'type' => 'string',
                'required' => true,
                'description' => 'Nombre de archivos o carpetas a buscar. Se puede buscar por nombre parcial o completo.'
            ],
            [
                'name' => 'ruta',
                'type' => 'string',
                'required' => false,
                'description' => 'Ruta de la carpeta donde empezar la búsqueda. Por defecto es la raíz de archivos.'
            ],
            [
                'name' => 'id_busqueda',
                'type' => 'string',
                'required' => false,
                'description' => 'Identificador único de la búsqueda. Si se omite, se inicia una nueva búsqueda. Si se envía, se continúa una búsqueda previa que quedó incompleta.'
            ],
            [
                'name' => 'token',
                'type' => 'string',
                'required' => false,
                'description' => 'Token de autenticación MCP para permisos de usuario.'
            ]
        ],
        'campos' => [
            'ubicacion' => ['type' => 'string', 'description' => 'Ruta completa del archivo o carpeta'],
            'contenido' => ['type' => 'string', 'description' => 'Contenido del archivo si es de texto plano (solo archivos)'],
            'es_carpeta' => ['type' => 'boolean', 'description' => 'Indica si es carpeta (1) o archivo (0)'],
            'permisos' => ['type' => 'string', 'description' => 'Permisos en formato numérico'],
            'group_id' => ['type' => 'integer', 'description' => 'ID de grupo propietario'],
            'user_id' => ['type' => 'integer', 'description' => 'ID de usuario propietario'],
            'oculto' => ['type' => 'boolean', 'description' => 'Si está oculto']
        ]
    ],
];
