<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class LibroTools extends BaseModelTools
{
    protected ?string $modelName = 'libro';
    protected ?string $modelClass = 'App\\Models\\Libro';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\LibrosController';
}
