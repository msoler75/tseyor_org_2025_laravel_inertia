<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Estados Disponibles para Inscripciones
    |--------------------------------------------------------------------------
    |
    | Define los estados posibles de una inscripción y sus transiciones
    |
    */
    'estados' => [
        'nueva' => [
            'etiqueta' => 'Nueva',
            'descripcion' => 'Nueva'
        ],
        'duplicada' => [
            'etiqueta' => 'Duplicada',
            'descripcion' => 'Inscripción Duplicada'
        ],
        'asignada' => [
            'etiqueta' => 'Asignada',
            'descripcion' => 'Asignada a un tutor o responsable'
        ],
        'rebotada' => [
            'etiqueta' => 'Rebotada',
            'descripcion' => 'Asignación Rebotada'
        ],
        'contactado' => [
            'etiqueta' => 'Contactado',
            'descripcion' => 'Se ha contactado con el/la inscrito/a'
        ],
        'encurso' => [
            'etiqueta' => 'En Curso',
            'descripcion' => 'Cursando o programado Curso Holístico'
        ],
        'abandonado' => [
            'etiqueta' => 'Abandonado',
            'descripcion' => 'Ha abandonado el curso o está ausente'
        ],
        'nocontesta' => [
            'etiqueta' => 'No Contesta',
            'descripcion' => 'No contesta mensajes o llamadas'
        ],
        'finalizado' => [
            'etiqueta' => 'Curso Finalizado',
            'descripcion' => 'Finalizado el Curso Holístico'
        ],
        'nointeresado' => [
            'etiqueta' => 'No Interesado',
            'descripcion' => 'No Interesado/a'
        ]
    ],

    'estados_no_elegibles' => [ 'nueva', 'rebotada' ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Notificaciones Automáticas
    |--------------------------------------------------------------------------
    |
    | Intervalos en días para el envío de notificaciones de seguimiento
    |
    */
    'notificaciones' => [
        // Días desde la asignación para la primera notificación de seguimiento
        'primer_seguimiento' => 3,

        // Días entre notificaciones de seguimiento posteriores
        // Cambia este valor a 14 para avisos cada dos semanas
        'intervalo_seguimiento' => 14,

        // Estados que requieren seguimiento automático
        'estados_seguimiento' => ['asignada', 'contactado', 'encurso'],

        // Estados que detienen el seguimiento automático
        'estados_finales' => ['finalizado', 'duplicada', 'nointeresado', 'rebotada', 'abandonado', 'nocontesta']
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Asignación Automática
    |--------------------------------------------------------------------------
    |
    | Configuración para la asignación automática de inscripciones
    |
    */
    'asignacion' => [
        // Rol que pueden recibir asignaciones de inscripciones
        // 'rol_elegible' => 'tutor',

        // Máximo de inscripciones activas por usuario
        'max_inscripciones_por_usuario' => 12,

        // Estados que cuentan como inscripciones "activas" para el límite
        'estados_activos' => ['asignada', 'contactado', 'encurso']
    ],

    /*
    |--------------------------------------------------------------------------
    | Configuración de Reportes para Administrador
    |--------------------------------------------------------------------------
    |
    | Configuración para reportes automáticos al administrador
    |
    */
    'reportes' => [
        // Email del administrador que recibe los reportes
        'admin_email' => env('ADMIN_EMAIL', 'admin@tseyor.org'),

        // Frecuencia de reportes (en días)
        'frecuencia_reporte' => 1,

        // Hora del día para enviar reportes (formato 24h)
        'hora_reporte' => '09:00'
    ],

    /*
    |--------------------------------------------------------------------------
    | Umbrales de Días para Avisos de Urgencia
    |--------------------------------------------------------------------------
    |
    | Define los días transcurridos después de los cuales se considera
    | que una inscripción requiere atención urgente según su estado
    |
    */
    'umbrales' => [
        // Días sin actualización para inscripciones asignadas (borde rojo)
        'asignado_urgente' => env('INSCRIPCIONES_UMBRAL_ASIGNADO', 7),

        // Días sin actualización para inscripciones contactadas (borde naranja)
        'contactado_urgente' => env('INSCRIPCIONES_UMBRAL_CONTACTADO', 7),

        // Días sin actualización para inscripciones en curso (borde amarillo)
        'encurso_seguimiento' => env('INSCRIPCIONES_UMBRAL_ENCURSO', 30),
    ]
];
