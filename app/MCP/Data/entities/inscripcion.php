<?php

return [
    'inscripcion' => [
        'descripcion' => 'Inscripciones al Curso Holístico Tseyor. Contiene datos personales — acceso restringido al controller.',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las inscripciones. Ejemplo: "María"',
            ],
            [
                'name' => 'estado',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por estado: "pendiente", "asignada", "contactada", "inscrita", "descartada".',
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "recientes"',
            ],
        ],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre completo'],
            'fecha_nacimiento' => ['type' => 'string', 'description' => 'Fecha de nacimiento (YYYY-MM-DD)'],
            'ciudad' => ['type' => 'string', 'description' => 'Ciudad'],
            'region' => ['type' => 'string', 'description' => 'Región o provincia'],
            'pais' => ['type' => 'string', 'description' => 'Código de país'],
            'email' => ['type' => 'string', 'description' => 'Correo electrónico'],
            'telefono' => ['type' => 'string', 'description' => 'Teléfono'],
            'comentario' => ['type' => 'string', 'description' => 'Comentario del solicitante'],
            'estado' => ['type' => 'string', 'description' => 'Estado: pendiente, asignada, contactada, inscrita, descartada'],
            'user_id' => ['type' => 'int', 'description' => 'ID del usuario asignado para seguimiento'],
            'notas' => ['type' => 'string', 'description' => 'Notas internas de seguimiento'],
        ],
    ],
];
