<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class GuiaTools extends BaseModelTools
{
    protected ?string $modelName = 'guia';
    protected ?string $modelClass = 'App\\Models\\Guia';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\GuiasController';
}
