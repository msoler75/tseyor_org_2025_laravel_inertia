<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class BoletinTools extends BaseModelTools
{
    protected ?string $modelName = 'boletin';
    protected ?string $modelClass = 'App\\Models\\Boletin';
    protected ?string $modelNamePlural = 'boletines';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\BoletinesController';

    protected array $methods = [
        'listar' => ['method' => 'index', 'args' => ['request']],
        'buscar' => ['method' => 'index', 'args' => ['request']],
        'ver' => ['method' => 'ver', 'args' => ['id']],
    ];
}
