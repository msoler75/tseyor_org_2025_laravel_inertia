<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class ComunicadoTools extends BaseModelTools
{
    protected ?string $modelName = 'comunicado';
    protected ?string $modelClass = 'App\\Models\\Comunicado';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\ComunicadosController';
}
