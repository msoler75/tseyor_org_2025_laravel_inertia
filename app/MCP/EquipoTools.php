<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class EquipoTools extends BaseModelTools
{
    protected ?string $modelName = 'equipo';
    protected ?string $modelClass = 'App\\Models\\Equipo';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\EquiposController';
    protected array $required = [
        'crear, editar, eliminar' => 'administrar equipos'
    ];
}
