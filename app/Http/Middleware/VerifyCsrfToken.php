<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'files/upload/image',
        'files/upload/file',
        '_sendbuild',
        '_sendssr',
        '_sendnodemodules',
        'usuarios/_comprobar_clave',
        'update-theme',
        // boletines
        'boletines/suscribir',
        'boletines/configurar/*',
        'boletines/desuscribir/*',
        'boletines/preparar',
        'boletines/enviar-pendientes',
        // Enlaces cortos API
        'obtener-enlace',
        // MCP API
        'mcp',
    ];
}
