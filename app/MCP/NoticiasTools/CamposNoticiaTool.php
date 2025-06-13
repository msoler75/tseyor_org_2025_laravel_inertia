<?php
namespace App\MCP\NoticiasTools;

use App\MCP\InfoCamposTool;
use App\MCP\BaseTool;

class CamposNoticiaTool extends BaseTool {
    protected string $name = 'campos_noticia';

    public function handle($params = []) {
        return [
            'fields' => InfoCamposTool::campos_noticia()
        ];
    }

    public function description(): string {
        return 'Devuelve la información de los campos del modelo Noticia.';
    }
}
