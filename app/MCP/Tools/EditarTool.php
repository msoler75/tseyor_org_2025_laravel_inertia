<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class EditarTool extends BaseTool
{

    public function __construct()
    {
        parent::__construct('editar');
    }

    public function handle($params)
    {
        Log::channel('mcp')->info('[MCP] Editar', ['params' => $params]);

        // determinar el modelo a partir de los parámetros
        $modelo = $params['entidad'] ?? null;
        if (!$modelo) return ['error' => 'Falta el parámetro entidad'];

        Log::channel('mcp')->info('[MCP] entidad: ' . $modelo);

        $toolsClass = $this->getModelToolsClass($modelo);
        if (!class_exists($toolsClass)) return ['error' => 'Clase no encontrada: ' . $toolsClass];

        $modelTools = new $toolsClass();
        $modelClass = $modelTools->getModelClass();
        $modelNameSingle = $modelTools->getModelNameSingle();

        $modelTools->checkMcpToken($params, $modelTools->getRequiredPermissions($this->name()));

        $id = $params['id'] ?? $params['slug'] ?? null;
        if (!$modelClass) {
            return ['error' => 'No se ha definido modelo'];
        }
        if (!isset($params['data'])) {
            return ['error' => 'No se han proporcionado datos para actualizar el elemento'];
        }
        if (is_numeric($id)) {
            $item = ($modelClass)::findOrFail($id);
        } else {
            $item = ($modelClass)::where('slug', $id)->firstOrFail();
        }
        $data = $params['data'];
        $data = $modelTools->onBeforeEdit($data, $item, $params);
        $item->update($data);
        $item = $modelTools->onAfterEdit($item, $params);

        return [$modelNameSingle . '_modificado' => $item->toArray()];
    }
}
