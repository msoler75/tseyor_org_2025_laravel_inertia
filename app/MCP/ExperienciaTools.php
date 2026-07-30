<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class ExperienciaTools extends BaseModelTools
{
    protected ?string $modelName = 'experiencia';
    protected ?string $modelClass = 'App\\Models\\Experiencia';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\ExperienciasController';
}
