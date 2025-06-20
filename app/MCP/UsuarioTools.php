<?php

namespace App\MCP;

use App\MCP\Base\BaseModelTools;

class UsuarioTools extends BaseModelTools
{
    protected ?string $modelName = 'usuario';
    protected ?string $modelClass = 'App\\Models\\User';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\UsuariosController';
    protected array $required = [
        'crear, editar, eliminar' => 'administrar usuarios'
    ];
}
