<?php

return [
    'boletin' => [
        'descripcion' => 'Boletines periódicos enviados a los suscriptores',
        'parametros_listar' => [
            ['name' => 'tipo', 'type' => 'string', 'required' => false, 'description' => 'Filtrar por tipo de boletín (semanal, mensual, etc.)'],
            ['name' => 'ano', 'type' => 'integer', 'required' => false, 'description' => 'Filtrar por año'],
            ['name' => 'mes', 'type' => 'integer', 'required' => false, 'description' => 'Filtrar por mes'],
            ['name' => 'enviado', 'type' => 'boolean', 'required' => false, 'description' => 'Filtrar por estado de envío'],
        ],
        'campos' => [
            'titulo' => ['type' => 'string', 'description' => 'Título del boletín'],
            'texto' => ['type' => 'string', 'description' => 'Contenido del boletín'],
            'dia' => ['type' => 'int', 'description' => 'Día'],
            'mes' => ['type' => 'int', 'description' => 'Mes'],
            'anyo' => ['type' => 'int', 'description' => 'Año'],
            'semana' => ['type' => 'int', 'description' => 'Semana'],
            'tipo' => ['type' => 'string', 'description' => 'Tipo de boletín'],
            'enviado' => ['type' => 'boolean', 'description' => 'Si el boletín ha sido enviado']
        ]
    ],
];
