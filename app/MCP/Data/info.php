<?php
// MCP/info.php

return [
    'comunicado' => [
        'descripcion'=> 'Comunicados dados por los hermanos mayores o amigos del espacio',
        'parametros_listar'=> [
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
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título del comunicado'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'fecha_comunicado' => ['type' => 'string', 'description' => 'Fecha (YYYY-MM-DD)'],
            'categoria' => ['type' => 'int', 'description' => 'Categoría numérica (0=General, 1=TAP, 2=12 del M., 3=Muul)'],
            'ano' => ['type' => 'int', 'description' => 'Año'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'entrada' => [
        'descripcion'=> 'Entradas del blog',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las entradas. Ejemplo: "Salud".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de entrada. Ejemplo: "Salud".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "cronologico".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 2.'
            ]
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título de la entrada'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'noticia' => [
        'descripcion'=> 'Últimas noticias y actualizaciones',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las noticias. Ejemplo: "COVID-19".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de noticia. Ejemplo: "Salud".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título de la noticia'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'audio' => [
        'descripcion'=> 'Audios disponibles para escuchar o descargar',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los audios. Ejemplo: "Meditación".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de audio. Ejemplo: "Relajación".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
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
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título del audio'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del audio'],
            'enlace' => ['type' => 'string', 'description' => 'Enlace externo (opcional)'],
            'audio' => ['type' => 'string', 'description' => 'Ruta o URL del archivo de audio'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
            'duracion' => ['type' => 'string', 'description' => 'Duración del audio (opcional)']
        ]
    ],
    'centro' => [
        'descripcion'=> 'Centros Tseyor: Casas Tseyor, Muulasterios y Pueblos Tseyor',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los centros. Ejemplo: "Andalucía".'
            ],
            [
                'name' => 'pais',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por país. Ejemplo: "ES".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "cronologico".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del centro'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'entradas' => ['type' => 'int', 'description' => 'Número de entradas asociadas'],
            'libros' => ['type' => 'int', 'description' => 'Número de libros asociados'],
            'poblacion' => ['type' => 'string', 'description' => 'Población'],
            'pais' => ['type' => 'string', 'description' => 'Código de país'],
            'contacto_id' => ['type' => 'int', 'description' => 'ID del contacto asociado']
        ]
    ],
    'contacto' => [
        'descripcion'=> 'Información de contacto de los centros',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los contactos. Ejemplo: "Juan".'
            ],
            [
                'name' => 'pais',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por país. Ejemplo: "ES".'
            ],
            [
                'name' => 'poblacion',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por población. Ejemplo: "Madrid".'
            ],
            [
                'name' => 'provincia',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por provincia. Ejemplo: "Madrid".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
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
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'equipo' => [
        'descripcion'=> 'Equipos de trabajo y colaboración',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los equipos. Ejemplo: "Desarrollo".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de equipo. Ejemplo: "Proyectos".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "cronologico".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del equipo'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del equipo'],
            'group_id' => ['type' => 'int', 'description' => 'ID del grupo asociado'],
            'anuncio' => ['type' => 'string', 'description' => 'Anuncio del equipo'],
            'reuniones' => ['type' => 'string', 'description' => 'Información sobre reuniones'],
            'informacion' => ['type' => 'string', 'description' => 'Información adicional'],
            'oculto' => ['type' => 'boolean', 'description' => 'Si el equipo está oculto'],
            'ocultarCarpetas' => ['type' => 'boolean', 'description' => 'Ocultar carpetas asociadas'],
            'ocultarArchivos' => ['type' => 'boolean', 'description' => 'Ocultar archivos asociados'],
            'ocultarMiembros' => ['type' => 'boolean', 'description' => 'Ocultar miembros del equipo'],
            'ocultarSolicitudes' => ['type' => 'boolean', 'description' => 'Ocultar solicitudes de membresía']
        ]
    ],
    'evento' => [
        'descripcion'=> 'Eventos y actividades programadas',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los eventos. Ejemplo: "Concierto".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de evento. Ejemplo: "Cultura".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
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
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título del evento'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del evento'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
            'fecha_inicio' => ['type' => 'string', 'description' => 'Fecha de inicio (YYYY-MM-DD)'],
            'fecha_fin' => ['type' => 'string', 'description' => 'Fecha de fin (YYYY-MM-DD)'],
            'hora_inicio' => ['type' => 'string', 'description' => 'Hora de inicio (HH:MM)'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador'],
            'centro_id' => ['type' => 'int', 'description' => 'ID del centro asociado'],
            'sala_id' => ['type' => 'int', 'description' => 'ID de la sala asociada'],
            'equipo_id' => ['type' => 'int', 'description' => 'ID del equipo organizador']
        ]
    ],
    'grupo' => [
        'descripcion'=> 'Grupos de usuarios para permisos especiales',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los grupos. Ejemplo: "Literatura".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del grupo'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve']
        ]
    ],
    'guia' => [
        'descripcion'=> 'Guías Estelares de Tseyor. Nuestros tutores del espacio',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las guías. Ejemplo: "Instalación".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de guía. Ejemplo: "Técnica".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
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
        ],
        'campos'=> [
            'nombre' => ['type' => 'string', 'description' => 'Nombre de la guía'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría de la guía'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'bibliografia' => ['type' => 'string', 'description' => 'Bibliografía asociada'],
            'libros' => ['type' => 'string', 'description' => 'Libros asociados'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'informe' => [
        'descripcion'=> 'Informes de los equipos: Actas, orden del día, resumenes, y otros informes y reportes generados',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los informes. Ejemplo: "Finanzas".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de informe. Ejemplo: "Anual".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título del informe'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del informe'],
            'equipo_id' => ['type' => 'int', 'description' => 'ID del equipo asociado'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'audios' => ['type' => 'array', 'description' => 'Lista de archivos de audio asociados'],
            'archivos' => ['type' => 'array', 'description' => 'Lista de archivos adjuntos'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'libro' => [
        'descripcion'=> 'Libros y lecturas recomendadas',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los libros. Ejemplo: "Cien años de soledad".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de libro. Ejemplo: "Novela".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
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
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título del libro'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del libro'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'edicion' => ['type' => 'string', 'description' => 'Edición del libro'],
            'paginas' => ['type' => 'int', 'description' => 'Número de páginas'],
            'pdf' => ['type' => 'string', 'description' => 'Ruta o URL del PDF'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'lugar' => [
        'descripcion'=> 'Lugares de interés en la Galaxia',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en los lugares. Ejemplo: "Parque".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de lugar. Ejemplo: "Parques".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del lugar'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría del lugar'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'libros' => ['type' => 'string', 'description' => 'Libros asociados'],
            'relacionados' => ['type' => 'string', 'description' => 'Lugares relacionados'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'meditacion' => [
        'descripcion'=> 'Meditaciones guiadas',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las meditaciones. Ejemplo: "Estrés".'
            ],
            [
                'name' => 'categoria',
                'type' => 'string',
                'required' => false,
                'description' => 'Filtrar por categoría de meditación. Ejemplo: "Relajación".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
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
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título de la meditación'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría de la meditación'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'audios' => ['type' => 'array', 'description' => 'Lista de archivos de audio asociados'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'normativa' => [
        'descripcion'=> 'Normativas y regulaciones aplicables en la comunidad Tseyor',
        'parametros_listar'=> [
            [
                'name' => 'buscar',
                'type' => 'string',
                'required' => false,
                'description' => 'Texto a buscar en las normativas. Ejemplo: "Seguridad".'
            ],
            [
                'name' => 'ano',
                'type' => 'integer',
                'required' => false,
                'description' => 'Filtrar por año. Ejemplo: 2023.'
            ],
            [
                'name' => 'orden',
                'type' => 'string',
                'required' => false,
                'description' => 'Orden de resultados: "recientes", "cronologico", "relevancia". Ejemplo: "relevancia".'
            ],
            [
                'name' => 'page',
                'type' => 'integer',
                'required' => false,
                'description' => 'Número de página para paginación (empieza en 1). Ejemplo: 1.'
            ]
        ],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título de la normativa'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'published_at' => ['type' => 'string', 'description' => 'Fecha de publicación (YYYY-MM-DD)'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'boletin' => [
        'descripcion'=> 'Boletines periódicos enviados a los suscriptores',
        'parametros_listar'=> [
            [ 'name' => 'tipo', 'type' => 'string', 'required' => false, 'description' => 'Filtrar por tipo de boletín (semanal, mensual, etc.)' ],
            [ 'name' => 'ano', 'type' => 'integer', 'required' => false, 'description' => 'Filtrar por año.' ],
            [ 'name' => 'mes', 'type' => 'integer', 'required' => false, 'description' => 'Filtrar por mes.' ],
            [ 'name' => 'enviado', 'type' => 'boolean', 'required' => false, 'description' => 'Filtrar por estado de envío.' ],
        ],
        'campos'=> [
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
    'pagina' => [
        'descripcion'=> 'Páginas estáticas del sitio',
        'parametros_listar'=> [],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título de la página'],
            'ruta' => ['type' => 'string', 'description' => 'Ruta de la página'],
            'atras_ruta' => ['type' => 'string', 'description' => 'Ruta de retroceso'],
            'atras_texto' => ['type' => 'string', 'description' => 'Texto de retroceso'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'palabras_clave' => ['type' => 'string', 'description' => 'Palabras clave SEO'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'psicografia' => [
        'descripcion'=> 'Psicografías y dibujos canalizados',
        'parametros_listar'=> [],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título de la psicografía'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'imagen' => ['type' => 'string', 'description' => 'Ruta o URL de la imagen']
        ]
    ],
    'sala' => [
        'descripcion'=> 'Salas virtuales y físicas para eventos',
        'parametros_listar'=> [],
        'campos'=> [
            'nombre' => ['type' => 'string', 'description' => 'Nombre de la sala'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'enlace' => ['type' => 'string', 'description' => 'Enlace de acceso']
        ]
    ],
    'termino' => [
        'descripcion'=> 'Términos y glosario Tseyor',
        'parametros_listar'=> [],
        'campos'=> [
            'nombre' => ['type' => 'string', 'description' => 'Nombre del término'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Definición o explicación'],
            'ref_terminos' => ['type' => 'string', 'description' => 'Referencias a otros términos'],
            'ref_libros' => ['type' => 'string', 'description' => 'Referencias a libros'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'tutorial' => [
        'descripcion'=> 'Tutoriales y guías prácticas',
        'parametros_listar'=> [],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título del tutorial'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'categoria' => ['type' => 'string', 'description' => 'Categoría'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'texto' => ['type' => 'string', 'description' => 'Contenido en markdown'],
            'video' => ['type' => 'string', 'description' => 'Enlace al video'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'video' => [
        'descripcion'=> 'Videos y grabaciones',
        'parametros_listar'=> [],
        'campos'=> [
            'titulo' => ['type' => 'string', 'description' => 'Título del video'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'descripcion' => ['type' => 'string', 'description' => 'Descripción breve'],
            'enlace' => ['type' => 'string', 'description' => 'Enlace al video'],
            'visibilidad' => ['type' => 'string', 'description' => '"P"=publicado, "B"=borrador']
        ]
    ],
    'user' => [
        'descripcion'=> 'Usuarios registrados en la plataforma',
        'parametros_listar'=> [
            [ 'name' => 'buscar', 'type' => 'string', 'required' => false, 'description' => 'Buscar por nombre o email.' ],
            [ 'name' => 'equipo_id', 'type' => 'integer', 'required' => false, 'description' => 'Filtrar por equipo.' ],
            [ 'name' => 'rol', 'type' => 'string', 'required' => false, 'description' => 'Filtrar por rol.' ],
        ],
        'campos'=> [
            'name' => ['type' => 'string', 'description' => 'Nombre del usuario'],
            'slug' => ['type' => 'string', 'description' => 'Slug único'],
            'email' => ['type' => 'string', 'description' => 'Correo electrónico'],
            'frase' => ['type' => 'string', 'description' => 'Frase de perfil'],
            'profile_photo_path' => ['type' => 'string', 'description' => 'Ruta de la foto de perfil'],
            'roles' => ['type' => 'array', 'description' => 'Roles asignados al usuario'],
            'equipos' => ['type' => 'array', 'description' => 'Equipos a los que pertenece'],
            'created_at' => ['type' => 'string', 'description' => 'Fecha de creación'],
            'updated_at' => ['type' => 'string', 'description' => 'Fecha de última actualización']
        ]
    ],
];
