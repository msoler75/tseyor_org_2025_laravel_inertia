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

        $this->checkMcpToken($params, $modelTools->getRequiredPermissions($this->name()));

        return $modelTools->onEliminar($params, $this);
    }
}
