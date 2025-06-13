<?php
namespace App\MCP\ComunicadosTools;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\MCP\BaseTool;



class ListarComunicadosTool extends BaseTool {
    protected string $name = 'listar_comunicados';

    // Métodos DRY heredados de BaseTool
    public function handle($params = []) {
        Log::channel('mcp')->info('[MCP-ListarComunicadosTool] INICIO listar', ['params' => $params]);
        try {
            // Limpiar y poblar el request heredado con los parámetros recibidos
            $this->request->query->replace($params);
            Log::channel('mcp')->info('[MCP-ListarComunicadosTool] Usando $this->request', ['request' => $this->request->all()]);
            $response = app(\App\Http\Controllers\ComunicadosController::class)->index($this->request);
            Log::channel('mcp')->info('[MCP-ListarComunicadosTool] Respuesta de index', ['response' => is_object($response) ? get_class($response) : gettype($response)]);
            $data = $this->fromInertiaToArray($response);
            Log::channel('mcp')->info('[MCP-ListarComunicadosTool] Resultado de fromInertiaToArray', ['data' => $data]);
            return $data;
        } catch (\Throwable $e) {
            Log::channel('mcp')->error('[MCP-ListarComunicadosTool] Error en listar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['error' => $e->getMessage()];
        }
    }
}
