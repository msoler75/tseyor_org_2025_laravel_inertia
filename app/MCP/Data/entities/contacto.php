<?php

return [
    'contacto' => [
        'descripcion' => 'Información de contacto de los centros',
        'parametros_listar' => [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los contactos. Ejemplo: "Juan"',
            ],
            [
                'name' => 'pais',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por país. Ejemplo: "ES"',
            ],
            [
                'name' => 'poblacion',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por población. Ejemplo: "Madrid"',
            ],
            [
                'name' => 'provincia',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por provincia. Ejemplo: "Madrid"',
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023',
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia"',
            ],
        ],
        'campos' => [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del contacto'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'pais' => ['type' => 'string', 'description' => 'Código de país'],
            'poblacion' => ['type' => 'string', 'description' => 'Población'],
            'provincia' => ['type' => 'string', 'description' => 'Provincia'],
            'direccion' => ['type' => 'string', 'description' => 'Dirección'],
            'codigo' => ['type' => 'string', 'description' => 'Código postal'],
            'telefono' => ['type' => 'string', 'description' => 'Teléfono'],
            'social' => ['type' => 'string', 'description' => 'Redes sociales'],
            'email' => ['type' => 'string', 'description' => 'Correo electrónico'],
            'latitud' => ['type' => 'string', 'description' => 'Latitud'],
            'longitud' => ['type' => 'string', 'description' => 'Longitud'],
            'centro_id' => ['type' => 'int', 'description' => 'ID del centro asociado'],
            'user_id' => ['type' => 'int', 'description' => 'ID del usuario asociado'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
        ],
    ],
];
