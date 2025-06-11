<?php
// MCP/BaseTool.php
namespace App\MCP;
use Illuminate\Http\Request;
use Inertia\Support\Header;
use Illuminate\Support\Arr;

abstract class BaseTool {

    protected $request;
    // constructor para inicializar el request
    public function __construct() {
        // Aquí podrías inicializar algo si es necesario
        $this->request = new Request();
        $this->request->headers->set(Header::INERTIA, 'true');
    }


    protected function fromInertiaToArray($response) {
        // si es un response de Inertia, extraer los datos
        if($response instanceof \Inertia\Response) {
            return $response->toResponse($this->request)->getData(true)['props'] ?? [];
        }
        // Si es una Response de Laravel
        /*if ($response instanceof \Illuminate\Http\Response || $response instanceof \Illuminate\Http\JsonResponse) {
            $content = $response->getContent();
            // Intentar decodificar como JSON
            $json = json_decode($content, true);
            if ($json !== null) return $json;
            // Si no es JSON, intentar extraer datos de Inertia
            if (method_exists($response, 'getOriginalContent')) {
                $original = $response->getOriginalContent();
                if (method_exists($original, 'getProps')) {
                    return $original->getProps();
                }
            }
            // Si no, devolver el contenido como string (fallback)
            return [ 'html' => $content ];
        }
        // Si es un objeto Inertia directamente
        if (method_exists($response, 'getProps')) {
            return $response->getProps();
        }
        if (method_exists($response, 'getData')) {
            return $response->getData();
        }
        // Si es array o stdClass, devuélvelo tal cual
        return $response;
        */
        throw new \InvalidArgumentException('Response type not supported for conversion to JSON.');
    }

    /**
     * Devuelve un cliente HTTP autenticado con la cookie de sesión de Laravel si se proporciona, y con asForm().
     * @param string|null $sessionCookie
     * @param string $url
     * @return \Illuminate\Http\Client\PendingRequest
     */
    protected static function withSessionCookie($sessionCookie, $url) {
        $http = \Illuminate\Support\Facades\Http::asForm();
        if ($sessionCookie) {
            $host = parse_url($url, PHP_URL_HOST) ?: 'localhost';
            return $http->withCookies(['laravel_session' => $sessionCookie], $host);
        }
        return $http;
    }

    protected function checkMcpToken($params, $permisos = ['administrar_contenidos']) {
        $token = $params['token'] ?? null;
        $tokens = config('mcp.tokens', []);
        // Permitir acceso si el token es el de 'administrar todo'
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
}
