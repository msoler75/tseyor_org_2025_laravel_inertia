<?php
// MCP/ComunicadosTool.php
namespace App\MCP;

use App\Models\Comunicado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ComunicadosTool extends BaseTool {
    public function listar($params = []) {
        Log::info('[MCP-ComunicadosTool] INICIO listar', ['params' => $params]);
        try {
            $request = new Request($params);
            Log::info('[MCP-ComunicadosTool] Creado Request', ['request' => $request->all()]);
            $response = app(\App\Http\Controllers\ComunicadosController::class)->index($request);
            Log::info('[MCP-ComunicadosTool] Respuesta de index', ['response' => is_object($response) ? get_class($response) : gettype($response)]);
            $data = self::fromInertiaToArray($response);
            Log::info('[MCP-ComunicadosTool] Resultado de fromInertiaToArray', ['data' => $data]);
            return $data;
        } catch (\Throwable $e) {
            Log::error('[MCP-ComunicadosTool] Error en listar: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return ['error' => $e->getMessage()];
        }
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
