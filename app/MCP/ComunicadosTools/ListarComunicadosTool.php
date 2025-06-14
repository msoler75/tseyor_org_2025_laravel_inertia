<?php
namespace App\MCP\ComunicadosTools;

use App\MCP\BaseListarTool;

class ListarComunicadosTool extends BaseListarTool {
    protected ?string $modelNameSingle = 'comunicado';
    protected ?string $controller = \App\Http\Controllers\ComunicadosController::class;

}
