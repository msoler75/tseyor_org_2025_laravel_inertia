<?php
namespace App\MCP\ComunicadosTools;

use App\Models\Comunicado;
use App\MCP\BaseTool;

class EliminarComunicadoTool extends BaseTool {
    protected string $name = 'eliminar_comunicado';

    // Métodos DRY heredados de BaseTool
    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            $comunicado = Comunicado::withTrashed()->findOrFail($id);
            if (!empty($params['force'])) {
                $comunicado->forceDelete();
                return ['comunicado_borrado' => true, 'id' => $id];
            }
            $comunicado->delete();
            return ['comunicado_borrado' => true, 'id' => $id];
        } catch (\Exception $e) {
            return ['error' => 'Error al eliminar el comunicado: ' . $e->getMessage()];
        }
    }
}
