<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class GrupoTools extends BaseModelTools
{
    protected ?string $modelName = 'grupo';
    protected ?string $modelClass = 'App\\Models\\Grupo';
    protected ?string $controllerClass = null;
    protected array $required = [
        'crear, editar, eliminar' => 'administrar usuarios'
    ];

    public function checkDeleteable($item, array $params = [])
    {
        // Verificar si existen nodos asociados a este grupo
        $nodosCount = \App\Models\Nodo::where('group_id', $item->id)->count();
        if ($nodosCount > 0) {
            throw new \Exception('No se puede eliminar el grupo porque tiene nodos asociados.');
        }
        // Verificar si existen usuarios asociados a este grupo
        $usuariosCount = $item->usuarios()->count();
        if ($usuariosCount > 0) {
            throw new \Exception('No se puede eliminar el grupo porque tiene usuarios asociados.');
        }
    }
}
