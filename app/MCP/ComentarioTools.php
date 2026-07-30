<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class ComentarioTools extends BaseModelTools
{
    protected ?string $modelName = 'comentario';
    protected ?string $modelClass = 'App\\Models\\Comentario';
    protected ?string $modelNamePlural = 'comentarios';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\Api\\ComentariosController';

    protected array $required = [
        'crear' => null,
        'editar, eliminar' => 'administrar social'
    ];
}
