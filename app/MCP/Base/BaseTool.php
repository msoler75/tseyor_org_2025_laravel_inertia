<?php

namespace App\MCP\Base;

use Illuminate\Http\Request;
use Inertia\Support\Header;
use Illuminate\Support\Arr;
use Inertia\Response as InertiaResponse;
use OPGG\LaravelMcpServer\Services\ToolService\ToolInterface;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Str;

abstract class BaseTool implements ToolInterface
{
    private ?string $toolName = null; // Nombre de la tool MCP (ej: 'editar_comunicado')
    private $inertiaRequest; // Objeto Request para respuestas Inertia

    public function __construct($toolName)
    {
        $this->toolName = $toolName;
        $this->inertiaRequest = new Request();
        // crear una sesión temporal para la petición Inertia
        $this->inertiaRequest->setLaravelSession(app('session.store'));
        $this->inertiaRequest->headers->set(Header::INERTIA, 'true');
    }

    public function fromInertiaToArray($response)
    {
        Log::channel('mcp')->info('[BaseTool] fromInertiaToArray', ['response_class' => is_object($response) ? get_class($response) : gettype($response)]);
        if ($response instanceof \Inertia\Response) {
            $data = $response->toResponse($this->inertiaRequest)->getData(true)['props'] ?? [];
            Log::channel('mcp')->info('[BaseTool] fromInertiaToArray props', ['props' => $data]);
            // si el objeto tiene 'listado' hacemos un cambio de idioma de las keys de paginación
            if (isset($data['listado']) && is_array($data['listado'])) {
                // $data['listado']['pagina_actual'] = $data['listado']['current_page'] ?? 1;
                $data['listado']['paginacion'] = [
                    'pagina_actual' => $data['listado']['current_page'] ?? 1,
                    'numero_paginas' => $data['listado']['last_page'] ?? 1,
                    'elementos_por_pagina' => $data['listado']['per_page'] ?? 1,
                    'total_elementos' => $data['listado']['total'] ?? 1,
                ];
                unset($data['listado']['current_page']);
                unset($data['listado']['last_page']);
                unset($data['listado']['last_page_url']);
                unset($data['listado']['first_page_url']);
                unset($data['listado']['from']);
                unset($data['listado']['to']);
                unset($data['listado']['path']);
                unset($data['listado']['per_page']);
                unset($data['listado']['next_page_url']);
                unset($data['listado']['prev_page_url']);
                unset($data['listado']['total']);
                unset($data['listado']['links']);
            }
            return $data;
        }
        // Si es una respuesta JSON, devolver el contenido decodificado
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $data = $response->getData(true);
            Log::channel('mcp')->info('[BaseTool] fromInertiaToArray json', ['json' => $data]);
            return $data;
        }
        // Si es un array, devolverlo tal cual
        if (is_array($response)) {
            return $response;
        }
        Log::channel('mcp')->error('[BaseTool] fromInertiaToArray: tipo de respuesta no soportado', ['response' => $response]);
        throw new \InvalidArgumentException('Response type not supported for conversion to JSON.');
    }

    /**
     * Verifica el token MCP contra los permisos requeridos para la acción.
     * Autentica el usuario si el token es un JWT válido.
     * $permisos es un string
     */
    public function checkMcpToken($params, $permisos = 'administrar contenidos')
    {
        $token = $params['token'] ?? null;
        $user = null;
        // Si el token parece un JWT (3 partes separadas por punto)
        if ($token && preg_match('/^[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+\.[A-Za-z0-9-_]+$/', $token)) {
            try {
                $jwtSecret = config('mcp-server.jwt_secret_prefix') . config('app.key');
                $decoded = \Firebase\JWT\JWT::decode($token, new \Firebase\JWT\Key($jwtSecret, 'HS256'));
                $userId = $decoded->sub ?? $decoded->user_id ?? null;
                if ($userId) {
                    $user = \App\Models\User::find($userId);
                    if ($user) {
                        auth()->setUser($user);
                    }
                }
                if (!$user)
                    abort(403, 'Token JWT válido pero usuario no encontrado');
            } catch (\Exception $e) {
                abort(403, 'Token JWT inválido: ' . $e->getMessage());
            }
        }
        // COMPROBAR SI CON EL USUARIO ACTUAL SE TIENE EL PERMISO REQUERIDO EN $permisos (string)
        if (empty($permisos)) {
            return true; // Si no hay permiso requerido, permitir
        }
        if(!is_string($permisos)) {
            Log::channel('mcp')->error('[BaseTool] checkMcpToken: permisos no es un string', ['permisos' => $permisos]);
            throw new \InvalidArgumentException('Error interno: permisos no es un string');
        }
        if ($user && !$user->can($permisos)) {
            abort(403, 'No tienes permisos');
        }
        return true;
    }

    /**
     * Normaliza los formatos de permisos a un array plano de strings.
     * Soporta string, array, arrays múltiples, y claves combinadas ("crear, editar, etc).
     * $toolName es el nombre de la acción actual (crear, editar, etc).
     */
    protected function normalizePermisos($permisos, $toolName = null)
    {
        // Si es null, vacío o []
        if (empty($permisos)) return [];
        // Si es string, devolver como array
        if (is_string($permisos)) return [$permisos];
        // Si es array plano de strings
        if (array_is_list($permisos) && count(array_filter($permisos, 'is_string')) === count($permisos)) {
            return $permisos;
        }
        // Si es array asociativo (formato sintético)
        $result = [];
        foreach ($permisos as $key => $value) {
            // Si la clave es numérica, es un array plano
            if (is_int($key)) {
                if (is_string($value)) {
                    $result[] = $value;
                } elseif (is_array($value)) {
                    $result = array_merge($result, $this->normalizePermisos($value));
                }
            } else {
                // Clave puede ser "crear, editar, eliminar" o similar
                $acciones = array_map('trim', explode(',', $key));
                if ($toolName && in_array($toolName, $acciones)) {
                    // El valor puede ser string o array
                    if (is_string($value)) {
                        $result[] = $value;
                    } elseif (is_array($value)) {
                        $result = array_merge($result, $this->normalizePermisos($value));
                    }
                }
            }
        }
        return $result;
    }

    protected function getCapabilityInfo(string $toolName): ?array
    {
        static $capabilities = null;
        if ($capabilities === null) {
            $capabilities = include __DIR__ . '/../Data/capabilities.php';
        }
        foreach ($capabilities as $tool) {
            if (isset($tool['name']) && $tool['name'] === $toolName) {
                return $tool;
            }
        }
        return null;
    }

    public function description(): string
    {
        $toolName = $this->name();
        Log::channel('mcp')->info('[BaseTool] description() buscando descripción para tool', ['toolName' => $toolName]);
        $info = $this->getCapabilityInfo($toolName);
        Log::channel('mcp')->info('[BaseTool] description() info encontrado', ['info' => $info]);
        return $info['description'] ?? '';
    }

    public function inputSchema(): array
    {
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

    public function annotations(): array
    {
        $info = $this->getCapabilityInfo($this->name());
        return $info['annotations'] ?? [];
    }

    public function name(): string
    {
        return $this->toolName;
    }

    public function execute(array $arguments): mixed
    {
        Log::channel('mcp')->info('[MCP] Ejecutando tool: ' . $this->name(), ['arguments' => $arguments]);
        try {
            return $this->handle($arguments);
        } catch (\Throwable $e) {
            // Si la excepción es 403 (token inválido), devolver evento de error de permisos MCP
            if ($e instanceof HttpException && $e->getStatusCode() === 403) {
                Log::channel('mcp')->error('' . $e->getMessage());
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
            // Para cualquier otra excepción, devolver como error MCP
            return [
                'error' => $e->getMessage(),
                'exception' => get_class($e),
                'code' => $e->getCode() ?: 500
            ];
        }
    }


    /**
     * $modelo es un nombre de modelo en minusculas (ej: 'comunicado')
     * @param mixed $modelo
     * @return string la clase de herramientas asociada al modelo (ej: \App\MCP\Tools\ComunicadosTools)
     */
    public function getModelToolsClass($modelo)
    {
        if (is_object($modelo)) {
            return Str::lower(class_basename($modelo));
        } elseif (is_string($modelo)) {
            return 'App\\MCP\\' . Str::ucfirst($modelo) . 'Tools';
        } else {
            throw new \InvalidArgumentException('El parámetro $modelo debe ser un objeto o una cadena de texto');
        }
    }


    public function getRequest(): Request
    {
        return $this->inertiaRequest;
    }
}
