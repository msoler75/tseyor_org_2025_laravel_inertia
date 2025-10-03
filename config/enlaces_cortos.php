<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Enlaces Cortos - Configuración
    |--------------------------------------------------------------------------
    |
    | Configuración para el sistema de acortador de enlaces de TSEYOR
    |
    */

    // URLs base donde se alojarán los enlaces cortos
    'dominios' => [
        'principal' => env('ENLACES_CORTOS_DOMINIO', env('APP_URL', 'https://tseyor.org')),
        // Dominios alternativos para enlaces cortos (ejemplos)
        'alternativos' => [
            // 'tsy.es',     // Dominio corto oficial
            // 'amor.es',    // Dominio temático
            // 'luz.info',   // Dominio espiritual
        ]
    ],

    // Prefijos para diferentes tipos de enlaces
    'prefijos' => [
        'por_defecto' => 'e',   // /e/abc123 (enlaces generales)
        'documento' => 'd',     // /d/abc123 (documentos PDF, DOCX, etc.)
        'audio' => 'a',         // /a/abc123 (archivos de audio MP3, WAV, etc.)
    ],

    // Configuración de umbrales para acortar automáticamente
    'umbrales' => [
        // Longitud mínima del PATH para que se acorte automáticamente
        // Ejemplo: /eventos/mi-evento-super-largo (sin contar dominio ni protocolo)
        'longitud_path' => env('ENLACES_CORTOS_UMBRAL_PATH', 30),

        // Longitud máxima de URL completa para considerar "corta"
        // URLs más cortas que esto nunca se acortarán
        'longitud_url_maxima_corta' => env('ENLACES_CORTOS_URL_MAXIMA_CORTA', 60),
    ],

    // Patrones de URLs que NO deben acortarse (son naturalmente cortas)
    'patrones_excluidos' => [
        // URLs con IDs numéricos (generalmente cortas)
        '\/informes\/\d{1,5}$',

        // URLs muy cortas con slugs cortos
        '\/[a-z]{1,8}\/[a-z0-9\-]{1,5}$',

        // URLs cortas
        '\/[a-z]{1,14}$',

        // Páginas principales
        '^\/$',
    ],

    // Configuración de códigos
    'codigo' => [
        'longitud' => env('ENLACES_CORTOS_CODIGO_LONGITUD', 6),
        'caracteres' => env('ENLACES_CORTOS_CARACTERES', 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789'),
        'excluir_similares' => true, // Excluir caracteres similares (0, O, o, l, I, i, 1)
    ],

    // Configuración de estadísticas
    'estadisticas' => [
        'habilitadas' => env('ENLACES_CORTOS_STATS_HABILITADAS', true),
        'rastrear_ip' => env('ENLACES_CORTOS_RASTREAR_IP', false), // Por privacidad
        'rastrear_user_agent' => env('ENLACES_CORTOS_RASTREAR_USER_AGENT', true),
        'rastrear_referer' => env('ENLACES_CORTOS_RASTREAR_REFERER', true),
    ],

    // TTL para enlaces temporales (en días)
    'ttl_temporal' => env('ENLACES_CORTOS_TTL_TEMPORAL', 30),

    // Cache de redirecciones (en minutos)
    'ttl_cache' => env('ENLACES_CORTOS_TTL_CACHE', 60),

    // Habilitar el sistema de enlaces cortos
    'habilitado' => env('ENLACES_CORTOS_HABILITADO', true),

    // Límites de velocidad para creación de enlaces
    'limite_velocidad' => [
        'max_por_hora' => env('ENLACES_CORTOS_MAX_POR_HORA', 100),
        'max_por_dia' => env('ENLACES_CORTOS_MAX_POR_DIA', 500),
    ],

    // Lista de dominios bloqueados (opcional)
    'dominios_bloqueados' => [
        // 'spam.com',
        // 'malware.net',
    ],

];
