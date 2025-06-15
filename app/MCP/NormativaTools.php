<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class NormativaTools extends BaseModelTools
{
    protected ?string $modelName = 'normativa';
    protected ?string $modelClass = 'App\\Models\\Normativa';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\NormativasController';
}
