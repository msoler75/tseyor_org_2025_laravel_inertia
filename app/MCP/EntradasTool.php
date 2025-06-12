<?php
// MCP/EntradasTool.php
namespace App\MCP;

use App\Http\Controllers\EntradasController;
use Illuminate\Http\Request;
use App\Models\Entrada;
use Illuminate\Support\Facades\Log;

class EntradasTool extends BaseTool {
    public function listar($params = []) {
        $request = new Request($params);
        // add header Header::INERTIA to $request
        $response = app(EntradasController::class)->index($request);
        Log::info('[MCP-EntradasTool] Respuesta de index', ['response' => is_object($response) ? get_class($response) : gettype($response)]);
        $data = self::fromInertiaToArray($response);
        Log::info('[MCP-EntradasTool] Resultado de fromInertiaToArray', ['data' => $data]);
        return $data;
    }
    public function ver($params) {
        $slug = $params['slug'] ?? null;
        $response = app(EntradasController::class)->show($slug);
        $data = $this->fromInertiaToArray($response);
        return $data;
    }
    public function crear($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        // Procesar imágenes en el texto antes de crear
        if (isset($params['texto'])) {
            $carpeta = (new Entrada)->getCarpetaMedios();
            $params['texto'] = \App\Pigmalion\Markdown::extraerImagenes($params['texto'], $carpeta);
        }
        // Crear directamente con Eloquent
        try {
            $entrada = Entrada::create($params);
            return $entrada ? ['entrada_creada'=>$entrada->toArray()] : [];
        }
        catch (\Exception $e) {
            // Manejo de errores, podrías lanzar una excepción o devolver un error específico
            return ['error' => 'Error al crear la entrada: ' . $e->getMessage()];
        }
    }
    public function actualizar($params) {
        $this->checkMcpToken($params, ['administrar_contenidos']);
        try {
            $id = $params['id'] ?? $params['slug'] ?? null;
            $entrada = Entrada::findOrFail($id);
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
    public function eliminar($params) {
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
