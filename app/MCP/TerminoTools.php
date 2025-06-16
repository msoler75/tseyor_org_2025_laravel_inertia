<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class TerminoTools extends BaseModelTools
{
    protected ?string $modelName = 'termino';
    protected ?string $modelClass = 'App\\Models\\Termino';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\TerminosController';
}
