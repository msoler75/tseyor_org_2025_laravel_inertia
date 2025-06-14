<?php
namespace App\MCP\ComunicadosTools;

use App\Models\Comunicado;
use App\MCP\BaseCrearTool;

class CrearComunicadoTool extends BaseCrearTool {
    protected ?string $modelNameSingle = 'comunicado';
    protected ?string $modelClass = Comunicado::class;


}
