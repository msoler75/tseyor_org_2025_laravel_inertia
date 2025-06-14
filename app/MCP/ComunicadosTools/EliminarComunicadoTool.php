<?php
namespace App\MCP\ComunicadosTools;

use App\Models\Comunicado;
use App\MCP\BaseEliminarTool;

class EliminarComunicadoTool extends BaseEliminarTool {
    protected ?string $modelNameSingle = 'comunicado';
    protected ?string $modelClass = Comunicado::class;
}
