<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class TutorialTools extends BaseModelTools
{
    protected ?string $modelName = 'tutorial';
    protected ?string $modelNamePlural = 'tutoriales';
    protected ?string $modelClass = 'App\\Models\\Tutorial';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\TutorialesController';
}
