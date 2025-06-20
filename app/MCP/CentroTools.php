<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class CentroTools extends BaseModelTools
{
    protected ?string $modelName = 'centro';
    protected ?string $modelClass = 'App\\Models\\Centro';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\CentrosController';
    protected array $required = [
        'crear, editar, eliminar' => 'administrar directorio'
    ];
}
