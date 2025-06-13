<?php
namespace App\MCP;

use Illuminate\Http\Request;
use Inertia\Support\Header;
use Illuminate\Support\Arr;
use Inertia\Response as InertiaResponse;
use OPGG\LaravelMcpServer\Services\ToolService\ToolInterface;

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
        return $info['parameters'] ?? [];
    }

    public function annotations(): array {
        $info = $this->getCapabilityInfo($this->name());
        return $info['annotations'] ?? [];
    }

    public function name(): string {
        return $this->name;
    }

    public function execute(array $arguments): mixed {
        return $this->handle($arguments);
    }
}
