<?php
namespace App\MCP;

use Illuminate\Support\Facades\Log;

abstract class BaseVerTool extends BaseTool {
    protected ?string $controllerMethod = 'show';

    public function __construct() {
        parent::__construct('ver');
    }

    public function handle($params) {
        try {
            Log::channel('mcp')->debug('[BaseVerTool] handle', ['params' => $params, 'controller' => $this->controller, 'controllerMethod' => $this->controllerMethod]);
            $id = $params['id'] ?? $params['slug'] ?? null;
            if ($this->controller) {
                // Llamar al método del controlador pasando el Request como primer argumento
                $response = app($this->controller)->{$this->controllerMethod}($this->inertiaRequest, $id);
                Log::channel('mcp')->debug('[BaseVerTool] Respuesta del controlador', ['response_class' => is_object($response) ? get_class($response) : gettype($response)]);
                $data = $this->fromInertiaToArray($response);
                Log::channel('mcp')->debug('[BaseVerTool] Datos extraídos', ['data' => $data]);
                return $data;
            } elseif ($this->modelClass) {
                if (is_numeric($id)) {
                    $item = ($this->modelClass)::findOrFail($id);
                } else {
                    $item = ($this->modelClass)::where('slug', $id)->firstOrFail();
                }
                Log::channel('mcp')->debug('[BaseVerTool] Item obtenido', ['item' => $item]);
                return [$this->modelNameSingle => $item->toArray()];
            }
            Log::channel('mcp')->error('[BaseVerTool] No se ha definido controller ni modelo');
            return ['error' => 'No se ha definido controller ni modelo'];
        } catch (\Throwable $e) {
            Log::channel('mcp')->error('[BaseVerTool] Excepción', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return ['error' => $e->getMessage()];
        }
    }
}
