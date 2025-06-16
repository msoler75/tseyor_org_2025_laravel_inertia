<?php

namespace App\MCP;

use App\MCP\Base\BaseModelTools;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaginaTools extends BaseModelTools
{
    protected ?string $modelName = 'pagina';
    protected ?string $modelClass = 'App\\Models\\Pagina';
    protected ?string $controllerClass = 'App\\Http\\Controllers\\PaginasController';


    public function callControllerMethod(string $toolName, Request $request, array $params)
    {
        $ruta = $params['ruta'] ?? null;
        if ($toolName == 'ver' && !$ruta)
            throw new \InvalidArgumentException('Debe especificar ruta');

        $page = $params['page'] ?? 1;
        $request->request->add(['page' => $page]);
        Log::channel('mcp')->info("[MCP] modelTools->callControllerMethod Request:", $request->all());

        $controller = $this->getControllerClass();
        $controllerMethod = $this->getMethod($toolName);
        if ($toolName == 'ver')
            return app($controller)->{$controllerMethod}($request, $ruta);
        else
            return app($controller)->{$controllerMethod}($request);
    }
}
