<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class MeditacionTools extends BaseModelTools
{
    protected ?string $modelName = 'meditacion';
    protected ?string $modelClass = 'App\\Models\\Meditacion';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\MeditacionesController';
}
