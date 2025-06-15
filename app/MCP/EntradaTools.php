<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class EntradaTools extends BaseModelTools
{
    protected ?string $modelName = 'entrada';
    protected ?string $modelClass = 'App\\Models\\Entrada';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\EntradasController';
}
