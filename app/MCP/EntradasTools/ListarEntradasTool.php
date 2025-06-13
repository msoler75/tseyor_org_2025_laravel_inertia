<?php
namespace App\MCP\EntradasTools;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\MCP\BaseTool;

class ListarEntradasTool extends BaseTool {
    protected string $name = 'listar_entradas';

    public function handle($params = []) {
        Log::channel('mcp')->info('[MCP-ListarEntradasTool] INICIO listar', ['params' => $params]);
        try {
            // Limpiar y poblar el request heredado con los parámetros recibidos
            $this->request->query->replace($params);
            $response = app(\App\Http\Controllers\EntradasController::class)->index($this->request);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } catch (\Throwable $e) {
            Log::channel('mcp')->error('[MCP-ListarEntradasTool] Error en listar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['error' => $e->getMessage()];
        }
    }
}
