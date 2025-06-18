<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class VerTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('ver');
    }

    public function handle($params)
    {
        Log::channel('mcp')->info('[MCP] Ver', ['params' => $params]);
        // determinar el modelo a partir de los parámetros
        $modelo = $params['entidad'] ?? null;
        if (!$modelo) return ['error' => 'Falta el parámetro entidad'];

        Log::channel('mcp')->info('[MCP] entidad: ' . $modelo);

        $toolsClass = $this->getModelToolsClass($modelo);
        if (!class_exists($toolsClass)) return ['error' => 'Clase no encontrada: ' . $toolsClass];

        $modelTools = new $toolsClass();
        // Validar token MCP si corresponde
        $this->checkMcpToken($params, $modelTools->getRequiredPermissions($this->name()));

        return $modelTools->onVer($params, $this);
    }


}
