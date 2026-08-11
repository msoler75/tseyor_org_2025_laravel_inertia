<?php

return [
    'termino' => [
        'descripcion' => 'Términos y glosario Tseyor',
        'parametros_listar' => [],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del término'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Definición o explicación'],
            'ref_terminos' => ['type' => 'string', 'description' => 'Referencias a otros términos'],
            'ref_libros' => ['type' => 'string', 'description' => 'Referencias a libros'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
        ],
    ],
];
