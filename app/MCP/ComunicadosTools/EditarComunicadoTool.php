<?php
namespace App\MCP\ComunicadosTools;

use App\Models\Comunicado;
use App\MCP\BaseEditarTool;

class EditarComunicadoTool extends BaseEditarTool {
    protected ?string $modelNameSingle = 'comunicado';
    protected ?string $modelClass = Comunicado::class;

}
