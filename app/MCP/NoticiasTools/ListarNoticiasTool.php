<?php
namespace App\MCP\NoticiasTools;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\MCP\BaseTool;

class ListarNoticiasTool extends BaseTool {
    protected string $name = 'listar_noticias';

    public function handle($params = []) {
        Log::info('[MCP-ListarNoticiasTool] INICIO listar', ['params' => $params]);
        try {
            $request = new Request($params);
            $response = app(\App\Http\Controllers\NoticiasController::class)->index($request);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } catch (\Throwable $e) {
            Log::error('[MCP-ListarNoticiasTool] Error en listar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['error' => $e->getMessage()];
        }
    }
}
