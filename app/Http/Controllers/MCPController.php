<?php
// MCPController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class MCPController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Intentar obtener tool y params de varias formas para máxima compatibilidad MCP
        $tool = null;
        $params = [];
        $json = null;

        // a) Si Content-Type es application/json, intentar decodificar el body
        if (str_contains($request->header('Content-Type'), 'application/json')) {
            $json = json_decode($request->getContent(), true);
            if (is_array($json)) {
                // Compatibilidad con JSON-RPC y variantes
                $tool = $json['tool'] ?? $json['method'] ?? null;
                $params = $json['params'] ?? [];
            }
        }

        // b) Si no se obtuvo tool, intentar por input tradicional (form, query, etc)
        if (!$tool) {
            $tool = $request->input('tool') ?? $request->input('method');
        }
        if (empty($params)) {
            $params = $request->input('params', []);
        }

        // c) Compatibilidad extra: si tool sigue sin estar, buscar en query string
        if (!$tool) {
            $tool = $request->query('tool') ?? $request->query('method');
        }
        if (empty($params)) {
            $params = $request->query('params', []);
        }

        Log::info('[MCPController] request recibido', ['request' => $request->all(), 'Content-Type' => $request->header('Content-Type')]);
        Log::info('[MCPController] tool recibido', ['tool' => $tool, 'params' => $params]);

        // Si no se recibe 'tool', asumir 'initialize' por defecto (para compatibilidad MCP)
        // 1. Si tool es null y params contiene handshake MCP, asumir 'initialize' (mantener params)
        if (!$tool && (isset($params['protocolVersion']) || isset($params['clientInfo']))) {
            $tool = 'initialize';
        } else if (!$tool) {
            // 2. Si tool es null y params está vacío, asumir 'initialize' y params vacíos
            $tool = 'initialize';
            $params = [];
        }

        Log::info('[MCPController] tool FINAL', ['tool' => $tool, 'params' => $params]);

        // Map tool name to class and method
        $toolMap = [
            // Comunicados
            'listar_comunicados' => ['App\\MCP\\ComunicadosTool', 'listar'],
            'ver_comunicado' => ['App\\MCP\\ComunicadosTool', 'ver'],
            'crear_comunicado' => ['App\\MCP\\ComunicadosTool', 'crear'],
            'actualizar_comunicado' => ['App\\MCP\\ComunicadosTool', 'actualizar'],
            'eliminar_comunicado' => ['App\\MCP\\ComunicadosTool', 'eliminar'],
            // Entradas
            'listar_entradas' => ['App\\MCP\\EntradasTool', 'listar'],
            'ver_entrada' => ['App\\MCP\\EntradasTool', 'ver'],
            'crear_entrada' => ['App\\MCP\\EntradasTool', 'crear'],
            'actualizar_entrada' => ['App\\MCP\\EntradasTool', 'actualizar'],
            'eliminar_entrada' => ['App\\MCP\\EntradasTool', 'eliminar'],
            // Noticias
            'listar_noticias' => ['App\\MCP\\NoticiasTool', 'listar'],
            'ver_noticia' => ['App\\MCP\\NoticiasTool', 'ver'],
            'crear_noticia' => ['App\\MCP\\NoticiasTool', 'crear'],
            'actualizar_noticia' => ['App\\MCP\\NoticiasTool', 'actualizar'],
            'eliminar_noticia' => ['App\\MCP\\NoticiasTool', 'eliminar'],
            // Info campos
            'campos_comunes' => ['App\MCP\InfoCamposTool', 'campos_comunes'],
            'campos_comunicado' => ['App\MCP\InfoCamposTool', 'campos_comunicado'],
            'campos_entrada' => ['App\MCP\InfoCamposTool', 'campos_entrada'],
            'campos_noticia' => ['App\MCP\InfoCamposTool', 'campos_noticia'],
            'capabilities' => ['App\MCP\InfoCamposTool', 'capabilities'],
            // MCP protocol handshake
            'initialize' => ['App\Http\Controllers\MCPController', 'mcp_initialize'],
        ];

        if (!isset($toolMap[$tool])) {
            return response()->json(['error' => 'Tool no encontrada'], 404);
        }

        [$class, $method] = $toolMap[$tool];
        // Si la tool es 'initialize', manejar directamente aquí en el controlador
        if ($tool === 'initialize') {
            Log::info('[MCPController] Inicializando MCP', ['params' => $params]);
            $capabilities = include base_path('app/MCP/capabilities.php');
            $result = [
                'name' => 'Teyor MCP Server',
                'version' => '1.0.0',
                'capabilities' => $capabilities,
            ];
            if (isset($params['protocolVersion'])) {
                $result['protocolVersion'] = $params['protocolVersion'];
            }
            return response(json_encode($result), 200)
                ->header('Content-Type', 'application/json');
        }

        if (!class_exists($class) || !method_exists($class, $method)) {
            return response()->json(['error' => 'Tool handler no implementado'], 500);
        }

        try {
            // Instanciar la clase y llamar al método como de instancia
            $instance = new $class();
            $result = $instance->$method($params);
            // Respuesta ultra estricta: solo el array plano, status 200, Content-Type exacto
            return response(json_encode($result), 200)
                ->header('Content-Type', 'application/json');
        } catch (\Throwable $e) {
            Log::error('[MCPController] Excepción en tool', [
                'tool' => $tool,
                'params' => $params,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Excepción en tool',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500)->header('Content-Type', 'application/json');
        }
    }



    public function mcp_initialize($params = [])
    {
        $capabilities = include __DIR__ . '/capabilities.php';
        $response = [
            'name' => 'Teyor MCP Server',
            'version' => '1.0.0',
            'capabilities' => $capabilities,
        ];
        if (isset($params['protocolVersion'])) {
            $response['protocolVersion'] = $params['protocolVersion'];
        }
        return $response;
    }
}
