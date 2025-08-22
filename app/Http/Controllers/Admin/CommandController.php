<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
            // 'inertia:stop-ssr', // Comando no disponible consistentemente en contexto web
        ],
        'exec' => [
            'pkill -f ssr',
            'inertia:start-ssr',
            'inertia:stop-ssr',
            'worker:stop',
            // './bash/ssr.sh start', // Ejecutar como comando externo
        ],
    ];

    public function runCommandPost(Request $request): JsonResponse
    {
        $command = $request->input('command');

        if (!$command) {
            return response()->json(['error' => 'Comando requerido'], 400);
        }

        return $this->runCommand($command);
    }

    public function runCommand(string $command): JsonResponse
    {
        $command = urldecode($command);
        $parts = preg_split('/\s+/', $command, -1, PREG_SPLIT_NO_EMPTY);
        $baseCommand = $parts[0];

        Log::info("Procesando comando", [
            'command' => $command,
            'baseCommand' => $baseCommand,
            'parts' => $parts
        ]);

        // Verificar si el comando está permitido y determinar su tipo
        $commandType = $this->getCommandType($baseCommand);

        // Si no se encontró por comando base, verificar comando completo para casos como "pkill -f ssr"
        if (!$commandType && count($parts) >= 3) {
            $fullCommand = implode(' ', $parts);
            foreach ($this->allowedCommands['exec'] as $allowedCommand) {
                if ($fullCommand === $allowedCommand) {
                    $commandType = 'exec';
                    break;
                }
            }
        }

        Log::info("Tipo de comando determinado", [
            'baseCommand' => $baseCommand,
            'commandType' => $commandType,
            'fullCommand' => $command
        ]);

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
            // Verificar si es un error de comando no encontrado
            if (strpos($e->getMessage(), 'does not exist') !== false) {
                Log::warning("Comando Artisan no encontrado: {$baseCommand}", [
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'status' => 'Comando no disponible',
                    'output' => "El comando '{$baseCommand}' no está disponible en el entorno web. Puede estar disponible solo en CLI.",
                    'exitCode' => 1
                ]);
            }

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

        if ($baseCommand === 'pkill' && isset($parts[1]) && $parts[1] === '-f' && isset($parts[2]) && $parts[2] === 'ssr') {
            $cmd = 'pkill -f ssr';
        } elseif ($baseCommand === 'inertia:stop-ssr') {
            $artisanPath = base_path('artisan');
            $cmd = 'php ' . escapeshellarg($artisanPath) . ' inertia:stop-ssr';
        } elseif ($baseCommand === 'inertia:start-ssr') {
            $artisanPath = base_path('artisan');
            $cmd = 'php ' . escapeshellarg($artisanPath) . ' inertia:start-ssr';
        } elseif ($baseCommand === 'worker:stop') {
            $scriptPath = base_path('bash/worker-stop.sh');
            $deployUser = config('app.deploy_user');
            $cmd = 'DEPLOY_USER=' . escapeshellarg($deployUser) . ' bash ' . escapeshellarg($scriptPath);
        } else {
            return response()->json(['error' => 'Comando exec no reconocido'], 400);
        }

        $output = [];
        $exitCode = 0;
        $outputText = '';

        try {
            exec($cmd . ' 2>&1', $output, $exitCode);
            $outputText = implode("\n", $output);
        } catch (\Exception $e) {
            Log::error("Error al ejecutar comando externo: {$cmd}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al ejecutar el comando',
                'output' => "Error del sistema: {$e->getMessage()}",
                'exitCode' => 1
            ], 500);
        }

        // Verificar errores específicos en la salida
        if (strpos($outputText, 'cannot fork') !== false ||
            strpos($outputText, 'Can not fork') !== false ||
            strpos($outputText, 'fork: retry: Resource temporarily unavailable') !== false ||
            strpos($outputText, 'Resource temporarily unavailable') !== false ||
            strpos($outputText, 'fork: Cannot allocate memory') !== false ||
            strpos($outputText, 'fork: retry: No child processes') !== false) {

            Log::error("Error de recursos del sistema detectado en comando: {$cmd}", [
                'output' => $outputText,
                'exitCode' => $exitCode
            ]);

            return response()->json([
                'error' => 'Error del sistema',
                'output' => 'El servidor no tiene recursos suficientes en este momento. Intenta de nuevo en unos segundos.',
                'exitCode' => $exitCode
            ], 503); // Service Unavailable
        }

        // Verificar errores de memoria
        if (strpos($outputText, 'Cannot allocate memory') !== false ||
            strpos($outputText, 'Out of memory') !== false ||
            strpos($outputText, 'Memory allocation failed') !== false) {

            Log::error("Error de memoria detectado en comando: {$cmd}", [
                'output' => $outputText,
                'exitCode' => $exitCode
            ]);

            return response()->json([
                'error' => 'Error de memoria',
                'output' => 'El servidor no tiene suficiente memoria disponible. Intenta de nuevo más tarde.',
                'exitCode' => $exitCode
            ], 503);
        }

        // Verificar errores de procesos
        if (strpos($outputText, 'No child processes') !== false ||
            strpos($outputText, 'Too many processes') !== false ||
            strpos($outputText, 'Process limit exceeded') !== false) {

            Log::error("Error de límite de procesos detectado en comando: {$cmd}", [
                'output' => $outputText,
                'exitCode' => $exitCode
            ]);

            return response()->json([
                'error' => 'Error de procesos',
                'output' => 'El servidor ha alcanzado el límite de procesos. Intenta de nuevo en unos minutos.',
                'exitCode' => $exitCode
            ], 503);
        }

        // Verificar otros errores comunes
        if (strpos($outputText, 'Permission denied') !== false) {
            Log::error("Error de permisos detectado en comando: {$cmd}", [
                'output' => $outputText,
                'exitCode' => $exitCode
            ]);

            return response()->json([
                'error' => 'Error de permisos',
                'output' => 'No tienes permisos suficientes para ejecutar este comando.',
                'exitCode' => $exitCode
            ], 403);
        }

        if (strpos($outputText, 'command not found') !== false || strpos($outputText, 'No such file') !== false) {
            Log::error("Comando no encontrado: {$cmd}", [
                'output' => $outputText,
                'exitCode' => $exitCode
            ]);

            return response()->json([
                'error' => 'Comando no encontrado',
                'output' => 'El comando solicitado no está disponible en este servidor.',
                'exitCode' => $exitCode
            ], 404);
        }

        Log::info("Comando externo ejecutado: {$cmd}", [
            'output' => $outputText,
            'exitCode' => $exitCode
        ]);

        // Para comandos como pkill, un exitCode mayor que 0 no siempre es un error
        $isSuccess = ($baseCommand === 'pkill') ? true : ($exitCode === 0);
        $status = $isSuccess ? 'Comando completado' : 'Comando completado con advertencias';

        return response()->json([
            'status' => $status,
            'output' => $outputText ?: $this->getDefaultMessage($fullCommand),
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

        // Algunos comandos pueden retornar códigos diferentes de 0 sin ser errores críticos
        $status = $exitCode === 0 ? 'Comando completado' : 'Comando completado con advertencias';

        return response()->json([
            'status' => $status,
            'output' => $output ?: "Comando {$baseCommand} ejecutado",
            'exitCode' => $exitCode
        ]);
    }

    private function getDefaultMessage(string $command): string
    {
        $messages = [
            'pkill' => 'Procesos SSR terminados correctamente',
            'inertia:stop-ssr' => 'Servidor SSR detenido',
            'inertia:start-ssr' => 'Servidor SSR iniciado'
        ];

        // Para comandos que empiecen con pkill
        if (strpos($command, 'pkill') === 0) {
            return 'Procesos terminados correctamente';
        }

        return $messages[$command] ?? 'Comando ejecutado correctamente';
    }
}
