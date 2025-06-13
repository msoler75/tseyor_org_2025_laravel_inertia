<?php
namespace App\MCP\NoticiasTools;

use Illuminate\Http\Request;
use App\MCP\BaseTool;

class VerNoticiaTool extends BaseTool {
    protected string $name = 'ver_noticia';

    public function handle($params) {
        try {
            $slug = $params['slug'] ?? null;
            $response = app(\App\Http\Controllers\NoticiasController::class)->show($slug);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()];
        }
    }
}
