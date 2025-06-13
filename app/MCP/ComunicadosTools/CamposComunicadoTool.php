<?php
namespace App\MCP\ComunicadosTools;

use App\MCP\BaseTool;

class CamposComunicadoTool extends BaseTool {
    protected string $name = 'campos_comunicado';

    public function handle($params = []) {
        // Devuelve los campos del modelo Comunicado usando el array asociativo de campos.php
        $campos = include __DIR__ . '/../campos.php';
        return [
            'fields' => $campos['comunicado']
        ];
    }

    public function description(): string {
        return 'Devuelve la información de los campos del modelo Comunicado.';
    }
}
