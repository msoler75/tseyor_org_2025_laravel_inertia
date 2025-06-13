<?php
namespace App\MCP\EntradasTools;

use App\Models\Entrada;
use App\MCP\BaseTool;

class EliminarEntradaTool extends BaseTool {
    protected string $name = 'eliminar_entrada';

    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            $entrada = Entrada::withTrashed()->findOrFail($id);
            if (!empty($params['force'])) {
                $entrada->forceDelete();
                return ['entrada_borrada' => true, 'id' => $id];
            }
            $entrada->delete();
            return ['entrada_borrada' => true, 'id' => $id];
        } catch (\Exception $e) {
            return ['error' => 'Error al eliminar la entrada: ' . $e->getMessage()];
        }
    }
}
