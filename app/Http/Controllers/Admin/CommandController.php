<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Pigmalion\DeployHelper;

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
            // 'pkill -f ssr',
            'inertia:start-ssr',
            'inertia:stop-ssr',
            'worker:stop',
            'ps -ef',
            // './bash/ssr.sh start', // Ejecutar como comando externo
        ],
    ];

    public function runCommandPost(Request $request): JsonResponse
    {
        $command = $request->input('command');

        if (!$command) {
            return response()->json(['error' => 'Comando requerido'], 400);
        }

        // Sanitización adicional de entrada
        $command = trim($command);
        if (preg_match('/[;&|`$]/', $command)) {
            Log::warning("Intento de inyección de comandos detectado", [
                'command' => $command,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            return response()->json(['error' => 'Caracteres no permitidos en el comando'], 400);
        }

        // Forzar HTTPS en producción
        if (app()->environment('production') && !$request->secure()) {
            return response()->json(['error' => 'HTTPS requerido para acceso remoto'], 426);
        }

        // Verificar token de deploy para acceso remoto
        $deployToken = config('deploy.deploy_token');
        $providedToken = $request->header('X-Deploy-Token');

        if ($providedToken) {
            // Verificar lista de IPs permitidas para acceso remoto
            $allowedIPs = DeployHelper::getAllowedIPs();
            if (!in_array($request->ip(), $allowedIPs)) {
                return response()->json(['error' => 'IP no autorizada para acceso remoto'], 403);
            }

            if (!$deployToken || $providedToken !== $deployToken) {
                return response()->json(['error' => 'Token de deploy inválido'], 403);
            }

            // Registrar automáticamente la IP del cliente remoto si no está en la lista
            $clientIP = $request->ip();
            if (!in_array($clientIP, $allowedIPs)) {
                DeployHelper::addAllowedIP($clientIP, 'remote-client');
                Log::info("IP registrada automáticamente para acceso remoto", ['ip' => $clientIP]);
            }
        } else {
            // Si no hay token, verificar que el usuario esté autenticado y sea admin (name == 'admin')
            if (!auth()->check() || auth()->user()->name !== 'admin') {
                return response()->json(['error' => 'Acceso denegado. Se requiere autenticación de administrador.'], 403);
            }
        }

        return $this->runCommand($command);
    }

    public function runCommand(string $command): JsonResponse
    {
        $request = request();
        $user = auth()->user();
        $ip = $request->ip();
        $userAgent = $request->userAgent();
        $tokenUsed = !empty($request->header('X-Deploy-Token'));

        // Log de auditoría
        Log::info("Comando ejecutado remotamente", [
            'command' => $command,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'token_used' => $tokenUsed,
            'user_id' => $user ? $user->id : null,
            'user_email' => $user ? $user->email : null,
            'timestamp' => now()->toISOString()
        ]);

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

        // Si no se encontró por comando base, verificar comando completo para casos como "pkill -f ssr" o "ps aux"
        if (!$commandType && count($parts) >= 2) {
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
            // Log de error de auditoría
            Log::error("Error al ejecutar comando - Auditoría", [
                'command' => $command,
                'error' => $e->getMessage(),
                'ip' => request()->ip(),
                'user_id' => auth()->id(),
                'timestamp' => now()->toISOString()
            ]);

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
            // Usar ps y kill como alternativa a pkill si no está disponible
            $cmd = 'ps aux | grep ssr | grep -v grep | awk \'{print $2}\' | xargs kill -9 2>/dev/null || pkill -f ssr 2>/dev/null || echo "No se pudieron matar procesos SSR"';
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
        } elseif ($baseCommand === 'ps' && isset($parts[1]) && $parts[1] === 'aux') {
            // adecuado en entornos BSD/Linux no interactivos
            $cmd = 'ps -ef';
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
                'output' => "Error del sistema: {$e->getMessage()}" . ($outputText ? "\n\nSalida del comando:\n{$outputText}" : ''),
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
                'output' => 'El servidor no tiene recursos suficientes en este momento. Intenta de nuevo en unos segundos.' . ($outputText ? "\n\nSalida del comando:\n{$outputText}" : ''),
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
                'output' => 'El servidor no tiene suficiente memoria disponible. Intenta de nuevo más tarde.' . ($outputText ? "\n\nSalida del comando:\n{$outputText}" : ''),
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
                'output' => 'El servidor ha alcanzado el límite de procesos. Intenta de nuevo en unos minutos.' . ($outputText ? "\n\nSalida del comando:\n{$outputText}" : ''),
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
                'output' => 'No tienes permisos suficientes para ejecutar este comando.' . ($outputText ? "\n\nSalida del comando:\n{$outputText}" : ''),
                'exitCode' => $exitCode
            ], 403);
        }

        // Verificar si el comando no está disponible en el entorno web
        if (strpos($outputText, 'no está disponible en el entorno web') !== false ||
            strpos($outputText, 'not available in web environment') !== false ||
            strpos($outputText, 'command not found') !== false) {
            Log::warning("Comando no disponible en entorno web: {$cmd}", [
                'output' => $outputText,
                'exitCode' => $exitCode
            ]);

            return response()->json([
                'error' => 'Comando no disponible',
                'output' => 'Este comando no está disponible en el entorno web actual. Puede estar disponible solo en línea de comandos (CLI).',
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
}
