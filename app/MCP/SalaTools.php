<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class SalaTools extends BaseModelTools
{
    protected ?string $modelName = 'sala';
    protected ?string $modelClass = 'App\\Models\\Sala';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\SalasController';
    protected array $required = [
        'crear, editar, eliminar' => 'administrar directorio'
    ];
}
