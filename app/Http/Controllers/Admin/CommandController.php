<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    protected $allowedCommands = [
        'optimize',
        'cache:clear',
        'view:clear',
        'imagecache:clear',
        'migrate',
        'sitemap:generate',
        'auth:clear-resets',
        'inertia:stop-ssr',
        'down',
        'up',
    ];

    public function runCommand(string $command): JsonResponse
    {
        $parts = preg_split('/\s+/', $command, -1, PREG_SPLIT_NO_EMPTY);
        $baseCommand = $parts[0];

        if (!in_array($baseCommand, $this->allowedCommands)) {
            return response()->json(['error' => 'Comando no permitido'], 403);
        }

        $parameters = [];
        for ($i = 1; $i < count($parts); $i++) {
            $part = $parts[$i];
            if (strpos($part, '--') === 0) {
                $key = substr($part, 2);
                $value = true;
                if ($i + 1 < count($parts) && strpos($parts[$i + 1], '--') !== 0) {
                    $value = $parts[$i + 1];
                    $i++;
                }
                $parameters[$key] = $value;
            } else {
                $parameters[] = $part;
            }
        }

        try {
            if ($baseCommand === 'inertia:stop-ssr') {
                // Usar ruta absoluta al archivo artisan
                $artisanPath = base_path('artisan');
                $cmd = 'php ' . escapeshellarg($artisanPath) . ' inertia:stop-ssr';
                $output = [];
                $exitCode = 0;
                exec($cmd . ' 2>&1', $output, $exitCode);
                $outputText = implode("\n", $output);
                Log::info("Comando externo ejecutado: {$cmd}", ['output' => $outputText, 'exitCode' => $exitCode]);
                return response()->json([
                    'status' => 'Comando completado',
                    'output' => $outputText,
                    'exitCode' => $exitCode
                ]);
            } else {
                Artisan::call($baseCommand, $parameters);
                $output = Artisan::output();
                $exitCode = Artisan::call($baseCommand, $parameters);
                $output = Artisan::output();
                Log::info("Comando Artisan ejecutado: {$baseCommand}", ['parameters' => $parameters, 'output' => $output, 'exitCode' => $exitCode]);
                return response()->json([
                    'status' => 'Comando completado',
                    'output' => $output,
                    'exitCode' => $exitCode
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Error al ejecutar el comando Artisan: {$baseCommand}", ['parameters' => $parameters, 'error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'error' => 'Error al ejecutar el comando',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
