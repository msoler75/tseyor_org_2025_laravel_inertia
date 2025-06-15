<?php

namespace App\MCP;

use App\MCP\Base\BaseModelTools;

class UserTools extends BaseModelTools
{
    protected ?string $modelName = 'user';
    protected ?string $modelClass = 'App\\Models\\User';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\UsersController';
}
