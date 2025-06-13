<?php
namespace App\MCP\NoticiasTools;

use App\Models\Noticia;
use App\MCP\BaseTool;

class EliminarNoticiaTool extends BaseTool {
    protected string $name = 'eliminar_noticia';

    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            $noticia = Noticia::withTrashed()->findOrFail($id);
            if (!empty($params['force'])) {
                $noticia->forceDelete();
                return ['noticia_borrada' => true, 'id' => $id];
            }
            $noticia->delete();
            return ['noticia_borrada' => true, 'id' => $id];
        } catch (\Exception $e) {
            return ['error' => 'Error al eliminar la noticia: ' . $e->getMessage()];
        }
    }
}
