<?php

namespace App\MCP\Tools;

use App\MCP\Base\BaseTool;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ConsultarMantenimientoTool extends BaseTool
{
    public function __construct()
    {
        parent::__construct('consultar_mantenimiento');
    }

    public function handle($params = [])
    {
        Log::channel('mcp')->info('[MCP] consultar_mantenimiento', ['params' => $params]);

        $token = $params['token'] ?? null;
        if ($token) {
            $this->checkMcpToken($params, ''); // sin permiso requerido para lectura
        }

        $setting = Setting::where('name', 'aviso_mantenimiento')->first();

        if (!$setting || !$setting->value || $setting->value === '{}') {
            return [
                'existe' => false,
                'estado' => 'sin_aviso',
                'mensaje' => 'No hay ningún aviso de mantenimiento configurado.',
            ];
        }

        $data = json_decode($setting->value, true);
        if (!$data || empty($data['inicio'])) {
            return [
                'existe' => false,
                'estado' => 'sin_aviso',
                'mensaje' => 'No hay ningún aviso de mantenimiento configurado.',
            ];
        }

        $ahora = Carbon::now('UTC');
        $inicio = Carbon::parse($data['inicio']);
        $fin = isset($data['fin']) ? Carbon::parse($data['fin']) : null;

        if ($fin && $fin->isPast()) {
            return [
                'existe' => true,
                'estado' => 'finalizado',
                'mensaje' => 'El último mantenimiento ya finalizó.',
                'datos' => $data,
            ];
        }

        if ($inicio->isFuture()) {
            return [
                'existe' => true,
                'estado' => 'proximo',
                'mensaje' => 'Hay un mantenimiento programado próximamente.',
                'datos' => $data,
                'inicio_relativo' => $inicio->diffForHumans(),
            ];
        }

        return [
            'existe' => true,
            'estado' => 'en_curso',
            'mensaje' => 'El mantenimiento está en curso ahora mismo.',
            'datos' => $data,
            'tiempo_restante' => $fin ? $fin->diffForHumans(null, true) : null,
        ];
    }
}
