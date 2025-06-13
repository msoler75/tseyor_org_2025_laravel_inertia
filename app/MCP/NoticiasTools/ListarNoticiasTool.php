<?php
namespace App\MCP\NoticiasTools;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\MCP\BaseTool;

class ListarNoticiasTool extends BaseTool {
    protected string $name = 'listar_noticias';

    public function handle($params = []) {
        Log::channel('mcp')->info('[MCP-ListarNoticiasTool] INICIO listar', ['params' => $params]);
        try {
            // Limpiar y poblar el request heredado con los parámetros recibidos
            $this->request->query->replace($params);
            $response = app(\App\Http\Controllers\NoticiasController::class)->index($this->request);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } catch (\Throwable $e) {
            Log::channel('mcp')->error('[MCP-ListarNoticiasTool] Error en listar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['error' => $e->getMessage()];
        }
    }
}
