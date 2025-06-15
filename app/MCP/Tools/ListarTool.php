<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class ListarTool extends BaseTool
{

    protected ?string $controllerMethod = 'index';

    public function __construct()
    {
        parent::__construct('listar');
    }

    public function handle($params = [])
    {
        Log::channel('mcp')->info('[MCP] Listar', ['params' => $params]);
        // determinar el modelo a partir de los parámetros
        $modelo = $params['entidad'] ?? null;
        if (!$modelo) return ['error' => 'Falta el parámetro entidad'];

        Log::channel('mcp')->info('[MCP] entidad: ' . $modelo);

        $toolsClass = $this->getModelToolsClass($modelo);
        if (!class_exists($toolsClass)) return ['error' => 'Clase no encontrada: ' . $toolsClass];

        $modelTools = new $toolsClass();
        $modelClass = $modelTools->getModelClass();
        $controller = $modelTools->getControllerClass();
        $controllerMethod = $modelTools->getMethod($this->name());
        $modelNamePlural = $modelTools->getModelNamePlural();

        if ($controller && $controllerMethod) {
            $this->inertiaRequest->query->replace($params);
            $response = app($controller)->{$controllerMethod}($this->inertiaRequest);
            $data = $this->fromInertiaToArray($response);
            return $data;
        } elseif ($modelClass) {
            $query = ($modelClass)::query();
            if (!empty($params['buscar'])) {
                $buscar = $params['buscar'];
                $query->where(function ($q) use ($buscar) {
                    $q->where('titulo', 'like', "%$buscar%")
                        ->orWhere('descripcion', 'like', "%$buscar%");
                });
            }
            if (!empty($params['categoria'])) {
                $query->where('categoria', $params['categoria']);
            }
            $result = $query->get();
            return [$modelNamePlural => $result->toArray()];
        }
        return ['error' => 'No se ha definido controller ni modelo'];
    }
}
