<?php
namespace App\MCP\ComunicadosTools;

use Illuminate\Http\Request;
use App\MCP\BaseTool;

class VerComunicadoTool extends BaseTool {
    protected string $name = 'ver_comunicado';

    // Métodos DRY heredados de BaseTool
    public function handle($params) {
        try {
            $slug = $params['slug'] ?? null;
            $response = app(\App\Http\Controllers\ComunicadosController::class)->show(new \Illuminate\Http\Request($params), $slug);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        }
    }
}
