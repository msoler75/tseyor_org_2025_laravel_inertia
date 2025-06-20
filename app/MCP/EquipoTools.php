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

    public function checkDeleteable($item, array $params = [])
    {
        // Verificar si existen miembros asociados
        if ($item->miembros()->count() > 0) {
            throw new \Exception('No se puede eliminar el equipo porque tiene miembros asociados.');
        }
        // Verificar si existen coordinadores asociados
        if ($item->coordinadores()->count() > 0) {
            throw new \Exception('No se puede eliminar el equipo porque tiene coordinadores asociados.');
        }
        // Verificar si existen carpetas asociadas
        if ($item->carpetas()->count() > 0) {
            throw new \Exception('No se puede eliminar el equipo porque tiene carpetas asociadas.');
        }

        // Verificar si existen informes asociados
        if ($item->informes()->count() > 0) {
            throw new \Exception('No se puede eliminar el equipo porque tiene informes asociados.');
        }
    }
}
