<?php
// MCP/NoticiasTool.php
namespace App\MCP;

use App\Models\Noticia;
use Illuminate\Http\Request;

class NoticiasTool extends BaseTool {
    public function listar($params = []) {
        $request = new Request($params);
        $response = app(\App\Http\Controllers\NoticiasController::class)->index($request);
        $data = self::fromInertiaToArray($response);
        return $data;
    }
    public function ver($params) {
        $slug = $params['slug'] ?? null;
        $response = app(\App\Http\Controllers\NoticiasController::class)->show($slug);
        $data = $this->fromInertiaToArray($response);
        return $data;
    }
    public function crear($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        if (isset($params['texto'])) {
            $carpeta = method_exists(Noticia::class, 'getCarpetaMedios') ? (new Noticia)->getCarpetaMedios() : null;
            if ($carpeta) {
                $params['texto'] = \App\Pigmalion\Markdown::extraerImagenes($params['texto'], $carpeta);
            }
        }
        try {
            $noticia = Noticia::create($params);
            // Aquí puedes lanzar jobs o lógica adicional si es necesario
            return $noticia ? ['noticia_creada'=>$noticia->toArray()] : [];
        }
        catch (\Exception $e) {
            return ['error' => 'Error al crear la noticia: ' . $e->getMessage()];
        }
    }
    public function actualizar($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            $noticia = Noticia::findOrFail($id);
            $data = $params['request'] ?? $params;
            if (isset($data['texto'])) {
                $carpeta = method_exists($noticia, 'getCarpetaMedios') ? $noticia->getCarpetaMedios() : null;
                if ($carpeta) {
                    $data['texto'] = \App\Pigmalion\Markdown::extraerImagenes($data['texto'], $carpeta);
                }
            }
            $noticia->update($data);
            // Aquí puedes lanzar jobs o lógica adicional si es necesario
            return ['noticia_modificada'=>$noticia->toArray()];
        } catch (\Exception $e) {
            return ['error' => 'Error al actualizar la noticia: ' . $e->getMessage()];
        }
    }
    public function eliminar($params) {
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
