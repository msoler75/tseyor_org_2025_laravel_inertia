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
    protected $request;
    protected string $name;

    public function __construct() {
        $this->request = new Request();
        $this->request->headers->set(Header::INERTIA, 'true');
    }

    protected function fromInertiaToArray($response) {
        if($response instanceof InertiaResponse) {
            return $response->toResponse($this->request)->getData(true)['props'] ?? [];
        }
        throw new \InvalidArgumentException('Response type not supported for conversion to JSON.');
    }

    protected function checkMcpToken($params, $permisos = ['administrar_contenidos']) {
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
        return $this->name;
    }

    public function execute(array $arguments): mixed {
        try {
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
