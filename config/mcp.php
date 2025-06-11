<?php
// config/mcp.php
return [

    'tokens' => [
        // Usa valores seguros y cámbialos en producción
        'administrar_contenidos' => env('MCP_TOKEN_ADMINISTRAR_CONTENIDOS', 'token-contenidos-123'),
        'administrar_social' => env('MCP_TOKEN_ADMINISTRAR_SOCIAL', 'token-social-456'),
        'administrar_todo' => env('MCP_TOKEN_ADMINISTRAR_TODO', 'token-todo-789'),
    ]
];
