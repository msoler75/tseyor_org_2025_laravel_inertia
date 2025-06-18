<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class ListarTool extends BaseTool
{
    protected ?string $controllerMethod = 'index';

    public function __construct()
    {
        parent::__construct('listar');
    }

    public function handle($params = [])
    {
        Log::channel('mcp')->info('[MCP] Listar', ['params' => $params]);
        $modelo = $params['entidad'] ?? null;
        if (!$modelo) return ['error' => 'Falta el parámetro entidad'];
        Log::channel('mcp')->info('[MCP] entidad: ' . $modelo);
        $toolsClass = $this->getModelToolsClass($modelo);
        if (!class_exists($toolsClass)) return ['error' => 'Clase no encontrada: ' . $toolsClass];
        $modelTools = new $toolsClass();
        $this->checkMcpToken($params, $modelTools->getRequiredPermissions($this->name()));
        return $modelTools->onListar($params, $this);
    }
}
