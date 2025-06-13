<?php
namespace App\MCP\NoticiasTools;

use App\Models\Noticia;
use App\MCP\BaseTool;

class EditarNoticiaTool extends BaseTool {
    protected string $name = 'editar_noticia';

    public function handle($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            $noticia = \App\Models\Noticia::findOrFail($id);
            $data = $params['request'] ?? $params;
            if (isset($data['texto'])) {
                $carpeta = method_exists($noticia, 'getCarpetaMedios') ? $noticia->getCarpetaMedios() : null;
                if ($carpeta) {
                    $data['texto'] = \App\Pigmalion\Markdown::extraerImagenes($data['texto'], $carpeta);
                }
            }
            $noticia->update($data);
            return ['noticia_modificada'=>$noticia->toArray()];
        } catch (\Exception $e) {
            return ['error' => 'Error al actualizar la noticia: ' . $e->getMessage()];
        }
    }
}
