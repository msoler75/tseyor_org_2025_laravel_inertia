<?php
// MCP/ComunicadosTool.php
namespace App\MCP;

use App\Models\Comunicado;
use Illuminate\Http\Request;

class ComunicadosTool extends BaseTool {
    public function listar($params = []) {
        $request = new Request($params);
        $response = app(\App\Http\Controllers\ComunicadosController::class)->index($request);
        $data = self::fromInertiaToArray($response);
        return $data;
    }
    public function ver($params) {
        $slug = $params['slug'] ?? null;
        $response = app(\App\Http\Controllers\ComunicadosController::class)->show(new Request($params), $slug);
        $data = $this->fromInertiaToArray($response);
        return $data;
    }
    public function crear($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        if (isset($params['texto'])) {
            $carpeta = (new Comunicado())->getCarpetaMedios();
            if ($carpeta) {
                $params['texto'] = \App\Pigmalion\Markdown::extraerImagenes($params['texto'], $carpeta);
            }
        }
        try {
            $comunicado = Comunicado::create($params);
            return $comunicado ? ['comunicado_creado'=>$comunicado->toArray()] : [];
        }
        catch (\Exception $e) {
            return ['error' => 'Error al crear el comunicado: ' . $e->getMessage()];
        }
    }
    public function actualizar($params) {
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
    public function eliminar($params) {
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
