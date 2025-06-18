<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use Illuminate\Support\Facades\Log;

class InfoTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('info');
    }

    public function handle($params = [])
    {
        Log::channel('mcp')->info('[MCP] info', ['params' => $params]);

        $entidades_info = include __DIR__ . '/../Data/info.php';
        // determinar el modelo a partir de los parámetros
        $modelo = $params['entidad'] ?? null;
        if (!$modelo)
        {
            $entidades=array_keys($entidades_info);
            sort($entidades);
            // en este caso devuelve una lista de entidades disponibles
            return [
                'entidades' => $entidades
            ];
        }

        Log::channel('mcp')->info('[MCP] entidad: ' . $modelo);

        $toolsClass = $this->getModelToolsClass($modelo);
        if (!class_exists($toolsClass)) return ['error' => 'Clase no encontrada: ' . $toolsClass];

        $modelTools = new $toolsClass();
        $modelNameSingle = $modelTools->getModelNameSingle();


        return [
            $modelNameSingle => $entidades_info[$modelNameSingle] ?? []
        ];
    }
}
