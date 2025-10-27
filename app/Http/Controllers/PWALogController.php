<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PWALogController extends Controller
{
    /**
     * Recibe logs de la PWA y los guarda en un archivo
     */
    public function store(Request $request)
    {
        try {
            // Log inicial para debugging
            Log::info('PWALogController: Recibida petición de log', [
                'all_data' => $request->all(),
                'headers' => $request->headers->all(),
                'method' => $request->method(),
                'url' => $request->fullUrl()
            ]);

            $data = $request->validate([
                'level' => 'required|string|in:info,warn,error,success',
                'message' => 'required|string',
                'data' => 'nullable',
                'url' => 'nullable|string',
                'timestamp' => 'nullable|string',
                'user_agent' => 'nullable|string',
                'is_pwa' => 'nullable|boolean'
            ]);

            Log::info('PWALogController: Validación exitosa', ['validated_data' => $data]);

            // Crear entrada de log
            $logEntry = sprintf(
                "[%s] [%s] %s - URL: %s - PWA: %s - Data: %s\n",
                $data['timestamp'] ?? now()->format('H:i:s'),
                strtoupper($data['level']),
                $data['message'],
                $data['url'] ?? 'unknown',
                $data['is_pwa'] ? 'YES' : 'NO',
                $data['data'] ? json_encode($data['data']) : 'null'
            );

            Log::info('PWALogController: Log entry creado', ['log_entry' => trim($logEntry)]);

            // Guardar en archivo de log específico para PWA
            $logFile = storage_path('logs/pwa-debug.log');
            $this->ensureLogDirectoryExists();

            Log::info('PWALogController: Guardando en archivo', ['log_file' => $logFile]);

            $result = file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

            if ($result === false) {
                Log::error('PWALogController: Error al escribir en archivo');
                throw new \Exception('No se pudo escribir en el archivo de log');
            }

            Log::info('PWALogController: Archivo escrito exitosamente', ['bytes_written' => $result]);

            // También guardar en Laravel logs
            Log::channel('pwa')->{$data['level']}($data['message'], [
                'url' => $data['url'],
                'data' => $data['data'],
                'is_pwa' => $data['is_pwa'],
                'user_agent' => $request->userAgent()
            ]);

            Log::info('PWALogController: Log guardado en canal PWA');

            return response()->json(['status' => 'ok']);

        } catch (\Exception $e) {
            Log::error('Error guardando log PWA', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Muestra los logs de PWA (solo en desarrollo)
     */
    public function show()
    {
        if (!app()->environment(['local', 'debug'])) {
            abort(404);
        }

        $logFile = storage_path('logs/pwa-debug.log');

        if (!file_exists($logFile)) {
            return response()->json(['logs' => [], 'message' => 'No hay logs disponibles']);
        }

        $logs = [];
        $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach (array_reverse($lines) as $line) {
            // Parsear la línea del log
            if (preg_match('/\[([^\]]+)\] \[([^\]]+)\] (.+) - URL: ([^ ]+) - PWA: ([^ ]+) - Data: (.+)/', $line, $matches)) {
                $logs[] = [
                    'timestamp' => $matches[1],
                    'level' => $matches[2],
                    'message' => $matches[3],
                    'url' => $matches[4],
                    'is_pwa' => $matches[5] === 'YES',
                    'data' => json_decode($matches[6], true)
                ];
            }
        }

        return response()->json([
            'logs' => $logs,
            'total' => count($logs),
            'file_size' => filesize($logFile)
        ]);
    }

    /**
     * Limpia los logs de PWA
     */
    public function clear()
    {
        if (!app()->environment(['local', 'debug'])) {
            abort(404);
        }

        $logFile = storage_path('logs/pwa-debug.log');

        if (file_exists($logFile)) {
            unlink($logFile);
        }

        return response()->json(['status' => 'ok', 'message' => 'Logs limpiados']);
    }

    /**
     * Asegura que el directorio de logs existe
     */
    private function ensureLogDirectoryExists()
    {
        $logDir = storage_path('logs');
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }
}
