<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class PublicacionTools extends BaseModelTools
{
    protected ?string $modelName = 'publicacion';
    protected ?string $modelClass = 'App\\Models\\Publicacion';
    protected ?string $modelNamePlural = 'publicaciones';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\PublicacionesController';
}
