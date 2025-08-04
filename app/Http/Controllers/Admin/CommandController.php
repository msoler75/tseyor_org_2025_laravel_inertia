<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;

class CommandController extends Controller
{
    protected $allowedCommands = [
        'artisan' => [
            'optimize',
            'cache:clear',
            'view:clear',
            'imagecache:clear',
            'migrate',
            'sitemap:generate',
            'auth:clear-resets',
            'db:backup',
            'down',
            'up',
            'inertia:stop-ssr',
        ],
        'exec' => [
            'pkill -f ssr',
        ],
    ];

    public function runCommand(string $command): JsonResponse
    {
        $parts = preg_split('/\s+/', $command, -1, PREG_SPLIT_NO_EMPTY);
        $baseCommand = $parts[0];

        // Verificar si el comando está permitido y determinar su tipo
        $commandType = $this->getCommandType($baseCommand);
        if (!$commandType) {
            return response()->json(['error' => 'Comando no permitido'], 403);
        }

        $parameters = $this->parseParameters($parts);

        try {
            if ($commandType === 'exec') {
                return $this->executeExternalCommand($command, $parts);
            } else {
                return $this->executeArtisanCommand($baseCommand, $parameters);
            }
        } catch (\Exception $e) {
            Log::error("Error al ejecutar el comando: {$baseCommand}", [
                'parameters' => $parameters,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'error' => 'Error al ejecutar el comando',
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    private function getCommandType(string $baseCommand): ?string
    {
        if (in_array($baseCommand, $this->allowedCommands['artisan'])) {
            return 'artisan';
        }

        if (in_array($baseCommand, $this->allowedCommands['exec'])) {
            return 'exec';
        }

        // Verificar comandos exec complejos como "pkill -f ssr"
        foreach ($this->allowedCommands['exec'] as $execCommand) {
            if (strpos($execCommand, $baseCommand) === 0) {
                return 'exec';
            }
        }

        return null;
    }

    private function parseParameters(array $parts): array
    {
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
        return $parameters;
    }

    private function executeExternalCommand(string $fullCommand, array $parts): JsonResponse
    {
        $baseCommand = $parts[0];

        if ($baseCommand === 'inertia:stop-ssr') {
            $artisanPath = base_path('artisan');
            $cmd = 'php ' . escapeshellarg($artisanPath) . ' inertia:stop-ssr';
        } elseif ($baseCommand === 'pkill' && isset($parts[1]) && $parts[1] === '-f' && isset($parts[2]) && $parts[2] === 'ssr') {
            $cmd = 'pkill -f ssr';
        } else {
            return response()->json(['error' => 'Comando exec no reconocido'], 400);
        }

        $output = [];
        $exitCode = 0;
        exec($cmd . ' 2>&1', $output, $exitCode);
        $outputText = implode("\n", $output);

        Log::info("Comando externo ejecutado: {$cmd}", [
            'output' => $outputText,
            'exitCode' => $exitCode
        ]);

        return response()->json([
            'status' => 'Comando completado',
            'output' => $outputText ?: $this->getDefaultMessage($baseCommand),
            'exitCode' => $exitCode
        ]);
    }

    private function executeArtisanCommand(string $baseCommand, array $parameters): JsonResponse
    {
        $exitCode = Artisan::call($baseCommand, $parameters);
        $output = Artisan::output();

        Log::info("Comando Artisan ejecutado: {$baseCommand}", [
            'parameters' => $parameters,
            'output' => $output,
            'exitCode' => $exitCode
        ]);

        return response()->json([
            'status' => 'Comando completado',
            'output' => $output,
            'exitCode' => $exitCode
        ]);
    }

    private function getDefaultMessage(string $command): string
    {
        $messages = [
            'pkill' => 'Procesos SSR terminados correctamente',
            'inertia:stop-ssr' => 'Servidor SSR detenido'
        ];

        return $messages[$command] ?? 'Comando ejecutado correctamente';
    }
}
