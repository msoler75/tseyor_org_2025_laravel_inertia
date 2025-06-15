<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class PaginaTools extends BaseModelTools
{
    protected ?string $modelName = 'pagina';
    protected ?string $modelClass = 'App\\Models\\Pagina';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\PaginasController';
}
