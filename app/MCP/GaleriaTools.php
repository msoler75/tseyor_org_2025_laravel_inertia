<?php

namespace App\MCP;

use App\MCP\Base\BaseModelTools;

class GaleriaTools extends BaseModelTools
{
    protected ?string $modelName = 'galeria';

    protected ?string $modelClass = 'App\\Models\\Galeria';

    protected ?string $controllerClass = 'App\\Http\\Controllers\\GaleriaController';
}
