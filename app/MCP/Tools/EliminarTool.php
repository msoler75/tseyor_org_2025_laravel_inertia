<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class EliminarTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('eliminar');
    }

    public function handle($params)
    {
        Log::channel('mcp')->info('[MCP] Eliminar', ['params' => $params]);

        // determinar el modelo a partir de los parámetros
        $modelo = $params['entidad'] ?? null;
        if (!$modelo) return ['error' => 'Falta el parámetro entidad'];

        Log::channel('mcp')->info('[MCP] entidad: ' . $modelo);

        $toolsClass = $this->getModelToolsClass($modelo);
        if (!class_exists($toolsClass)) return ['error' => 'Clase no encontrada: ' . $toolsClass];

        $modelTools = new $toolsClass();
        $modelClass = $modelTools->getModelClass();
        $modelNameSingle = $modelTools->getModelNameSingle();
        $required = $modelTools->getRequiredPermissions($this->name());

        $this->checkMcpToken($params, $required);

        $id = $params['id'] ?? $params['slug'] ?? null;
        if (! $id) {
            return ['error' => 'No se ha proporcionado un ID o slug para eliminar el elemento'];
        }
        if (!$modelClass) {
            return ['error' => 'No se ha definido modelo'];
        }
        if (is_numeric($id)) {
            $item = ($modelClass)::findOrFail($id);
        } else {
            $item = ($modelClass)::where('slug', $id)->firstOrFail();
        }
        Log::channel('mcp')->info('[MCP] encontrado para eliminar', ['id' => $item->id]);
        // Hook de validación previa al borrado
        $modelTools->checkDeleteable($item, $params);
        if (!empty($params['force']) && method_exists($item, 'forceDelete')) {
            $item->forceDelete();
            Log::channel('mcp')->info('[MCP] eliminado con forceDelete', ['id' => $item->id]);
            return [$modelNameSingle . '_borrado' => true, 'id' => $id];
        }
        $item->delete();
        Log::channel('mcp')->info('[MCP] eliminado con delete (soft)', ['id' => $item->id]);
        return [$modelNameSingle . '_borrado' => true, 'id' => $id];
    }
}
