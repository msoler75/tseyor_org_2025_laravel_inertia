<?php

/**
 * Documentación de los settings de tipo "anuncio" en la tabla `settings`.
 *
 * La tabla `settings` (modelo App\Models\Setting) almacena configuraciones del sitio como pares clave-valor.
 *
 * Campos de la tabla:
 *   - name:        Clave única que identifica el setting
 *   - description: Descripción legible
 *   - value:       Valor (texto o JSON)
 *
 * =============================================================================
 * SETTINGS DE TIPO ANUNCIO
 * =============================================================================
 *
 * 1. anuncio (name = 'anuncio')
 *    - value: string (HTML permitido)
 *    - Descripción: Texto libre que se muestra en el banner superior del sitio.
 *    - Comportamiento: Visible siempre hasta que el usuario lo cierra manualmente (se guarda en sesión).
 *    - Uso: Anuncios genéricos, avisos, mensajes importantes.
 *    - Ejemplo value: "Nueva sección de <strong>Meditaciones</strong> disponible"
 *
 * 2. aviso_mantenimiento (name = 'aviso_mantenimiento')
 *    - value: JSON con los siguientes campos:
 *
 *    {
 *        "titulo":                 "string (requerido) - Título corto para el banner",
 *        "descripcion":            "string (opcional) - Descripción breve",
 *        "inicio":                 "string ISO 8601 UTC (requerido) - Fecha/hora de inicio del mantenimiento",
 *        "fin":                    "string ISO 8601 UTC (requerido) - Fecha/hora estimada de finalización",
 *        "duracion_estimada":      "string (opcional) - Duracion estimada de la caida (ej: 'aproximadamente 1 hora')",
 *        "zona_horaria_original":  "string (opcional) - Zona horaria del aviso original (ej: 'America/Los_Angeles')",
 *        "url_info":               "string (opcional) - URL a página con más información",
 *        "raw_email_text":         "string (opcional) - Texto original del email de DreamHost (para referencia)"
 *    }
 *
 *    - Comportamiento AUTO-CADUCABLE:
 *      * Antes de 'inicio':     Se muestra como "Próximo mantenimiento: ..."
 *      * Entre 'inicio' y 'fin': Se muestra como "En curso: ..."
 *      * Después de 'fin':       NO se muestra (desaparece automáticamente)
 *
 *    - Ejemplo value:
 *    {
 *        "titulo": "Mantenimiento programado: Actualización de Ubuntu 22.04 a 24.04",
 *        "descripcion": "El sitio puede estar inaccesible entre 1 y 6 horas durante la madrugada.",
 *        "inicio": "2026-08-05T07:00:00Z",
 *        "fin": "2026-08-05T13:00:00Z",
 *        "duracion_estimada": "aproximadamente 1 hora",
 *        "zona_horaria_original": "America/Los_Angeles",
 *        "url_info": "https://tseyor.org/mantenimiento/agosto-2026",
 *        "raw_email_text": "Hola MARCEL SOLER,\n\nActualizaremos el sistema operativo..."
 *    }
 *
 * =============================================================================
 * PRIORIDAD DE VISUALIZACIÓN EN EL BANNER
 * =============================================================================
 *
 * El componente Announcement.vue muestra en este orden:
 *   1. Si existe 'anuncio' (texto) → se muestra el anuncio genérico
 *   2. Si no, y existe 'aviso_mantenimiento' vigente/próximo → se muestra el aviso de mantenimiento
 *   3. Si no hay nada → no se muestra banner
 *
 * =============================================================================
 * GESTIÓN
 * =============================================================================
 *
 * - Backpack: /admin/aviso-mantenimiento/edit
 *   Permite editar el aviso manualmente o pegar un email de DreamHost y parsearlo con IA (OpenRouter).
 *
 * - MCP Tools:
 *   * analizar_mantenimiento:  Recibe texto de email → devuelve JSON estructurado (usa OpenRouter)
 *   * programar_mantenimiento: Recibe datos JSON estructurados → guarda en settings
 *     (El agente MCP extrae los campos del email por sí mismo)
 *
 * - MCP Agent flow (sin OpenRouter):
 *   1. Agente recibe email de DreamHost
 *   2. Agente extrae titulo, inicio, fin, etc. del texto
 *   3. Agente llama a programar_mantenimiento con { datos: { titulo, inicio, fin, ... } }
 *   4. El aviso se guarda y aparece en el sitio
 */

return [
    'settings' => [
        'anuncio' => [
            'type' => 'texto_html',
            'descripcion' => 'Anuncio genérico en el banner del sitio',
            'auto_caducable' => false,
        ],
        'aviso_mantenimiento' => [
            'type' => 'json',
            'descripcion' => 'Aviso de mantenimiento programado auto-caducable',
            'auto_caducable' => true,
            'campos' => ['titulo', 'descripcion', 'inicio', 'fin', 'zona_horaria_original', 'url_info', 'raw_email_text'],
        ],
    ],
];
