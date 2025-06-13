<?php
namespace App\MCP\EntradasTools;

use App\Models\Entrada;
use App\MCP\BaseTool;

class EditarEntradaTool extends BaseTool {
    protected string $name = 'editar_entrada';

    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            if (is_numeric($id)) {
                $entrada = Entrada::findOrFail($id);
            } else {
                $entrada = Entrada::where('slug', $id)->firstOrFail();
            }
            $data = $params['request'] ?? $params;
            if (isset($data['texto'])) {
                $carpeta = $entrada->getCarpetaMedios();
                $data['texto'] = \App\Pigmalion\Markdown::extraerImagenes($data['texto'], $carpeta);
            }
            $entrada->update($data);
            return ['entrada_modificada'=>$entrada->toArray()];
        } catch (\Exception $e) {
            return ['error' => 'Error al actualizar la entrada: ' . $e->getMessage()];
        }
    }
}
