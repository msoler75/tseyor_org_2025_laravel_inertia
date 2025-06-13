<?php
namespace App\MCP\ComunicadosTools;

use App\MCP\InfoCamposTool;
use App\MCP\BaseTool;

class CamposComunicadoTool extends BaseTool {
    protected string $name = 'campos_comunicado';

    public function handle($params = []) {
        // Devuelve los campos del modelo Comunicado usando InfoCamposTool
        return [
            'fields' => InfoCamposTool::campos_comunicado()
        ];
    }

    public function description(): string {
        return 'Devuelve la información de los campos del modelo Comunicado.';
    }
}
