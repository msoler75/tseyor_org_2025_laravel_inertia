<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class AudioTools extends BaseModelTools
{
    protected ?string $modelName = 'audio';
    protected ?string $modelClass = 'App\\Models\\Audio';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\AudiosController';

}
