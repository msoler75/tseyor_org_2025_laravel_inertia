<?php
namespace App\MCP\EntradasTools;

use App\MCP\BaseTool;

class CamposEntradaTool extends BaseTool {
    protected string $name = 'campos_entrada';

    public function handle($params = []) {
        $campos = include __DIR__ . '/../campos.php';
        return [
            'fields' => $campos['entrada']
        ];
    }

    public function description(): string {
        return 'Devuelve la información de los campos del modelo Entrada.';
    }
}
