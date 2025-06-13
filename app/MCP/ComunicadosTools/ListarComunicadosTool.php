<?php
namespace App\MCP\ComunicadosTools;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\MCP\BaseTool;



class ListarComunicadosTool extends BaseTool {
    protected string $name = 'listar_comunicados';

    // Métodos DRY heredados de BaseTool
    public function handle($params = []) {
        Log::info('[MCP-ListarComunicadosTool] INICIO listar', ['params' => $params]);
        try {
            $request = new Request($params);
            Log::info('[MCP-ListarComunicadosTool] Creado Request', ['request' => $request->all()]);
            $response = app(\App\Http\Controllers\ComunicadosController::class)->index($request);
            Log::info('[MCP-ListarComunicadosTool] Respuesta de index', ['response' => is_object($response) ? get_class($response) : gettype($response)]);
            $data = $this->fromInertiaToArray($response);
            Log::info('[MCP-ListarComunicadosTool] Resultado de fromInertiaToArray', ['data' => $data]);
            return $data;
        } catch (\Throwable $e) {
            Log::error('[MCP-ListarComunicadosTool] Error en listar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['error' => $e->getMessage()];
        }
    }
}
