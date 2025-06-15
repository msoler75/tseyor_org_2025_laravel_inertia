<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class InformeTools extends BaseModelTools
{
    protected ?string $modelName = 'informe';
    protected ?string $modelClass = 'App\\Models\\Informe';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\InformesController';
}
