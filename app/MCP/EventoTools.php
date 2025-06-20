<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class EventoTools extends BaseModelTools
{
    protected ?string $modelName = 'evento';
    protected ?string $modelClass = 'App\\Models\\Evento';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\EventosController';
    protected array $required = [
        'crear, editar, eliminar' => 'administrar social'
    ];
}
