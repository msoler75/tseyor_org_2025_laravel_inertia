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
            if (is_numeric($id)) {
                $comunicado = Comunicado::withTrashed()->findOrFail($id);
            } else {
                $comunicado = Comunicado::withTrashed()->where('slug', $id)->firstOrFail();
            }
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
