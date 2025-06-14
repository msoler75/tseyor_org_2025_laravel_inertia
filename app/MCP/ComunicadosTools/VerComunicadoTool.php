<?php
namespace App\MCP\ComunicadosTools;

use App\MCP\BaseVerTool;

class VerComunicadoTool extends BaseVerTool {
    protected ?string $modelNameSingle = 'comunicado';
    protected ?string $controller = \App\Http\Controllers\ComunicadosController::class;
}
