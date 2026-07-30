<?php

return [
    'comentario' => [
        'descripcion' => 'Comentarios de usuarios en las páginas de contenido. Organizados en estructura de árbol (respuestas anidadas).',
        'parametros_listar' => [
            [
                'name' => 'url',
                'type' => 'string',
                'required' => true,
                'description' => 'URL de la página de la que obtener comentarios. Ejemplo: "/libros/el-amor-la-clave-hermetica"'
            ]
        ],
        'campos' => [
            'url' => ['type' => 'string', 'description' => 'URL de la página donde se publicó el comentario'],
            'texto' => ['type' => 'string', 'description' => 'Contenido del comentario'],
            'user_id' => ['type' => 'int', 'description' => 'ID del usuario autor'],
            'respuesta_a' => ['type' => 'int', 'description' => 'ID del comentario al que responde (null si es raíz)'],
            'eliminado' => ['type' => 'boolean', 'description' => 'Si el comentario está marcado como eliminado']
        ]
    ],
];
