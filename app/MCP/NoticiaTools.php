<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class NoticiaTools extends BaseModelTools
{
    protected ?string $modelName = 'noticia';
    protected ?string $modelClass = 'App\\Models\\Noticia';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\NoticiasController';
}
