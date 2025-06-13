<?php
namespace App\MCP\EntradasTools;

use Illuminate\Http\Request;
use App\MCP\BaseTool;

class VerEntradaTool extends BaseTool {
    protected string $name = 'ver_entrada';

    public function handle($params) {
        try {
            $slug = $params['slug'] ?? null;
            $response = app(\App\Http\Controllers\EntradasController::class)->show($slug);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        }
    }
}
