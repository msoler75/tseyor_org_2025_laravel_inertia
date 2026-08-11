<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class AnunciarMantenimientoTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('anunciar_mantenimiento');
    }

    public function handle($params = [])
    {
        Log::channel('mcp')->info('[MCP] anunciar_mantenimiento', ['params' => $params]);

        $this->checkMcpToken($params, 'administrar contenidos');

        $datos = $params['datos'] ?? $params['data'] ?? null;

        if (empty($datos) || ! is_array($datos)) {
            return [
                'error' => 'Se requiere el parámetro "datos" con un objeto JSON con los campos del aviso de mantenimiento',
                'campos_esperados' => [
                    'titulo' => 'string (requerido) - Título del aviso',
                    'descripcion' => 'string (opcional) - Descripción breve',
                    'inicio' => 'string ISO 8601 (requerido) - Fecha/hora de inicio en UTC. Ej: "2026-08-05T07:00:00Z"',
                    'fin' => 'string ISO 8601 (requerido) - Fecha/hora estimada de fin en UTC',
                    'duracion_estimada' => 'string (opcional) - Duracion estimada de la caida, ej: "aproximadamente 1 hora"',
                    'url_info' => 'string (opcional) - URL con más información',
                    'raw_email_text' => 'string (opcional) - Texto original del email para referencia',
                ],
            ];
        }

        if (empty($datos['titulo'])) {
            return ['error' => 'El campo "titulo" es requerido en los datos'];
        }
        if (empty($datos['inicio'])) {
            return ['error' => 'El campo "inicio" (ISO 8601 UTC) es requerido en los datos'];
        }
        if (empty($datos['fin'])) {
            return ['error' => 'El campo "fin" (ISO 8601 UTC) es requerido en los datos'];
        }

        $data = array_filter([
            'titulo' => $datos['titulo'] ?? '',
            'descripcion' => $datos['descripcion'] ?? '',
            'inicio' => $datos['inicio'] ?? null,
            'fin' => $datos['fin'] ?? null,
            'zona_horaria_original' => $datos['zona_horaria_original'] ?? '',
            'duracion_estimada' => $datos['duracion_estimada'] ?? '',
            'url_info' => $datos['url_info'] ?? '',
            'raw_email_text' => $datos['raw_email_text'] ?? '',
        ], fn ($v) => $v !== null && $v !== '');

        $setting = Setting::firstOrNew(['name' => 'aviso_mantenimiento']);
        $setting->description = $data['titulo'];
        $setting->value = json_encode($data, JSON_UNESCAPED_UNICODE);
        $setting->save();

        return [
            'aviso_mantenimiento_guardado' => true,
            'datos' => $data,
        ];
    }
}
