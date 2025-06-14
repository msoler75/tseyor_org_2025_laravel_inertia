<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class VerTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('ver');
    }

    public function handle($params)
    {
        Log::channel('mcp')->info('[MCP] Ver', ['params' => $params]);
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
        $modelNameSingle = $modelTools->getModelNameSingle();
        $id = $params['id'] ?? $params['slug'] ?? null;
        Log::channel('mcp')->debug('[BaseVerTool] handle', ['params' => $params, 'modelo' => $modelo, 'controller' => $controller, 'modelClass' => $modelClass]);
        if ($controller && $controllerMethod) {
            if (!class_exists($controller)) return ['error' => 'Clase no encontrada: ' . $controller];
            $reflection = new \ReflectionMethod($controller, $controllerMethod);
            $paramsReflection = $reflection->getParameters();
            if (count($paramsReflection) === 2 && $paramsReflection[0]->getType() && $paramsReflection[0]->getType()->getName() === \Illuminate\Http\Request::class) {
                $response = app($controller)->{$controllerMethod}($this->inertiaRequest, $id);
            } else {
                $response = app($controller)->{$controllerMethod}($id);
            }
            $data = $this->fromInertiaToArray($response);
            return $data;
        } elseif ($modelClass) {
            if (is_numeric($id)) {
                $item = ($modelClass)::findOrFail($id);
            } else {
                $item = ($modelClass)::where('slug', $id)->firstOrFail();
            }
            return [$modelNameSingle => $item->toArray()];
        }
        return ['error' => 'No se ha definido controller ni modelo'];
    }
}
