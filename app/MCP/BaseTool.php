<?php
namespace App\MCP;

use Illuminate\Http\Request;
use Inertia\Support\Header;
use Illuminate\Support\Arr;
use Inertia\Response as InertiaResponse;
use OPGG\LaravelMcpServer\Services\ToolService\ToolInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

abstract class BaseTool implements ToolInterface {
    private ?string $toolName = null; // Nombre de la tool MCP (ej: 'editar_comunicado')
    protected $inertiaRequest; // Objeto Request para respuestas Inertia
    protected ?string $modelClass = null; // Clase del modelo Eloquent (ej: \App\Models\Comunicado)
    protected ?string $modelNameSingle = null; // Nombre singular del modelo (ej: 'comunicado')
    protected ?string $modelNamePlural = null; // Nombre plural del modelo (ej: 'comunicados')

    protected ?string $controller = null; // Ejemplo: \App\Http\Controllers\EventosController
    protected ?string $controllerMethod = null; // Ejemplo: 'index' o 'show'
    protected array $permisos = []; // Ejemplo: ['administrar_contenidos']

    public function __construct($verb, $plural = false) {
        if(!$this->modelNamePlural) {
            $this->modelNamePlural = $this->modelNameSingle ? $this->modelNameSingle . 's' : null;
        }
        $this->toolName = $verb . '_' . ($plural ? $this->modelNamePlural : $this->modelNameSingle);
        $this->inertiaRequest = new Request();
        $this->inertiaRequest->headers->set(Header::INERTIA, 'true');
        Log::channel('mcp')->info('[BaseTool] Instanciado ' . static::class, [
            'toolName' => $this->toolName,
            'modelClass' => $this->modelClass,
            'modelNameSingle' => $this->modelNameSingle,
            'modelNamePlural' => $this->modelNamePlural,
            'controller' => $this->controller,
            'controllerMethod' => $this->controllerMethod,
        ]);
    }

    protected function fromInertiaToArray($response) {
        Log::channel('mcp')->info('[BaseTool] fromInertiaToArray', ['response_class' => is_object($response) ? get_class($response) : gettype($response)]);
        if($response instanceof InertiaResponse) {
            $data = $response->toResponse($this->inertiaRequest)->getData(true)['props'] ?? [];
            Log::channel('mcp')->info('[BaseTool] fromInertiaToArray props', ['props' => $data]);
            return $data;
        }
        Log::channel('mcp')->error('[BaseTool] fromInertiaToArray: tipo de respuesta no soportado', ['response' => $response]);
        throw new \InvalidArgumentException('Response type not supported for conversion to JSON.');
    }

    protected function checkMcpToken($params, $permisos = ['administrar_contenidos']) {
        if(empty($permisos)) {
            return true; // Si no hay permisos, no hacemos nada
        }
        Log::channel('mcp')->info('[MCP] Verificando token para permisos: ' . implode(', ', $permisos), ['params' => $params]);
        if (empty($params['token'])) {
            abort(403, 'Token no proporcionado');
        }
        $token = $params['token'] ?? null;
        $tokens = config('mcp-server.tokens', []);
        $tokenTodo = Arr::get($tokens, 'administrar_todo');
        if ($token && $tokenTodo && $token === $tokenTodo) {
            return true;
        }
        foreach ($permisos as $permiso) {
            $permisoToken = Arr::get($tokens, $permiso);
            if ($token && $permisoToken && $token === $permisoToken) {
                return true;
            }
        }
        abort(403, 'Token inválido o insuficiente para el permiso requerido');
    }

    protected function getCapabilityInfo(string $toolName): ?array {
        static $capabilities = null;
        if ($capabilities === null) {
            $capabilities = include __DIR__ . '/capabilities.php';
        }
        foreach ($capabilities as $group) {
            if (!empty($group['tools'])) {
                foreach ($group['tools'] as $tool) {
                    if ($tool['name'] === $toolName) {
                        return $tool;
                    }
                }
            }
        }
        return null;
    }

    public function description(): string {
        $info = $this->getCapabilityInfo($this->name());
        return $info['description'] ?? '';
    }

    public function inputSchema(): array {
        $info = $this->getCapabilityInfo($this->name());
        $params = $info['parameters'] ?? [];
        // Si ya es un objeto JSON Schema, lo devolvemos tal cual
        if (isset($params['type']) && $params['type'] === 'object') {
            return $params;
        }
        // Si es un array de parámetros clásico, lo convertimos a JSON Schema
        if (is_array($params) && !empty($params) && isset($params[0]['name'])) {
            $properties = [];
            $required = [];
            foreach ($params as $param) {
                $prop = [
                    'type' => $param['type'] ?? 'string',
                    'description' => $param['description'] ?? ''
                ];
                $properties[$param['name']] = $prop;
                if (!empty($param['required'])) {
                    $required[] = $param['name'];
                }
            }
            $schema = [
                'type' => 'object',
                'properties' => $properties
            ];
            if (!empty($required)) {
                $schema['required'] = $required;
            }
            return $schema;
        }
        // Si no hay parámetros o es un array vacío
        // Forzar que properties sea SIEMPRE un array asociativo vacío
        return [
            'type' => 'object',
            'properties' => (object)[]
        ];
    }

    public function annotations(): array {
        $info = $this->getCapabilityInfo($this->name());
        return $info['annotations'] ?? [];
    }

    public function name(): string {
        return $this->toolName;
    }

    public function execute(array $arguments): mixed {
        try {
            $this->checkMcpToken($arguments, $this->permisos);
            return $this->handle($arguments);
        } catch (\Throwable $e) {
            // Si la excepción es 403 (token inválido), devolver evento de error de permisos MCP
            if ($e instanceof HttpException && $e->getStatusCode() === 403) {

                Log::channel('mcp')->error(''. $e->getMessage());

                // Convención MCP: devolver array con error de permisos
                return [
                    'error' => 'PERMISSION_DENIED',
                    'message' => $e->getMessage() ?? 'Token inválido o permisos insuficientes',
                    'code' => 403
                ];
            }
            Log::channel('mcp')->error('[MCP] Excepción en ' . $this->name() . ': ' . $e->getMessage(), [
                'exception' => $e,
                'arguments' => $arguments,
            ]);
            throw $e;
        }
    }
}
