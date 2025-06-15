<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class CrearTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('crear');
    }

    public function handle($params)
    {
        Log::channel('mcp')->info('[MCP] Crear', ['params' => $params]);

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

        if (!isset($params['data'])) {
            return ['error' => 'No se han proporcionado datos para actualizar el elemento'];
        }
        $data = $params['data'];
        $data = $modelTools->onBeforeCreate($data, $params);
        $item = ($modelClass)::create($data);
        $item = $modelTools->onAfterCreate($item, $params);
        return $item ? [$modelNameSingle . '_creado' => $item->toArray()] : [];
    }
}
