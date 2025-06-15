<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class LugarTools extends BaseModelTools
{
    protected ?string $modelName = 'lugar';
    protected ?string $modelClass = 'App\\Models\\Lugar';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\LugaresController';
}
