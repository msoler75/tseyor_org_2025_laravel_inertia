<?php
namespace App\MCP\ComunicadosTools;

use App\Models\Comunicado;
use App\MCP\BaseTool;

class EditarComunicadoTool extends BaseTool {
    protected string $name = 'editar_comunicado';

    // Métodos DRY heredados de BaseTool
    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            $comunicado = Comunicado::findOrFail($id);
            $data = $params['request'] ?? $params;
            if (isset($data['texto'])) {
                $carpeta = $comunicado->getCarpetaMedios();
                if ($carpeta) {
                    $data['texto'] = \App\Pigmalion\Markdown::extraerImagenes($data['texto'], $carpeta);
                }
            }
            $comunicado->update($data);
            return ['comunicado_modificado'=>$comunicado->toArray()];
        } catch (\Exception $e) {
            return ['error' => 'Error al actualizar el comunicado: ' . $e->getMessage()];
        }
    }
}
