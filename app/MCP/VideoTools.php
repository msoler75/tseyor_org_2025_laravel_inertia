<?php
namespace App\MCP;
use App\MCP\Base\BaseModelTools;

class VideoTools extends BaseModelTools
{
    protected ?string $modelName = 'video';
    protected ?string $modelClass = 'App\\Models\\Video';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\VideosController';
}
