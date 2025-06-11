<?php
// MCPController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class MCPController extends Controller
{
    public function handle(Request $request)
    {
        $tool = $request->input('tool');
        $params = $request->input('params', []);

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
        ];

        if (!isset($toolMap[$tool])) {
            return response()->json(['error' => 'Tool no encontrada'], 404);
        }

        [$class, $method] = $toolMap[$tool];
        if (!class_exists($class) || !method_exists($class, $method)) {
            return response()->json(['error' => 'Tool handler no implementado'], 500);
        }

        try {
            $result = $class::$method($params);
            // Si la respuesta es una Response de Laravel, devuélvela como JSON
            if ($result instanceof \Illuminate\Http\Response || $result instanceof \Illuminate\Http\JsonResponse) {
                return response()->json(json_decode($result->getContent(), true));
            }
            // Si es un objeto renderizado por Inertia, extrae los datos
            if (method_exists($result, 'getData')) {
                return response()->json($result->getData());
            }
            // Si es un array o stdClass, devuélvelo como JSON
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Excepción en tool',
                'message' => $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTrace() : null
            ], 500);
        }
    }
}
