<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class InscripcionTools extends BaseModelTools
{
    protected ?string $modelName = 'inscripcion';
    protected ?string $modelClass = 'App\\Models\\Inscripcion';
    protected ?string $modelNamePlural = 'inscripciones';

    protected array $required = [
        'crear' => null,
        'editar, eliminar' => 'administrar contenidos'
    ];
}
