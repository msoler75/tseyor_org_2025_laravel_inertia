<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class InformeTools extends BaseModelTools
{
    protected ?string $modelName = 'informe';
    protected ?string $modelClass = 'App\\Models\\Informe';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\InformesController';
    protected array $required = [
        'crear, editar, eliminar' => 'administrar equipos'
    ];

    public function checkDeleteable($item, array $params = [])
    {
        // Verificar si el informe está asociado a un equipo
        if ($item->equipo()->exists()) {
            throw new \Exception('No se puede eliminar el informe porque está asociado a un equipo.');
        }
    }
}
