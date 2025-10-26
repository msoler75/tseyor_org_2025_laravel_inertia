<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RemoteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remote:command
                            {cmd* : Comando a ejecutar}
                            {--local : para desarrollo usa localhost como URL del servidor (opcional)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta comandos remotamente en el servidor usando token de deploy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $commandParts = $this->argument('cmd');
        $command = implode(' ', $commandParts);

        $token = config('deploy.deploy_token');
        $local = $this->option('local');

        if ($local) {
            // Usar localhost para desarrollo
            $url = 'http://localhost:80/admin/command2';
        } else {
            $url = config('deploy.url', 'http://localhost') . '/admin/command2';
        }

        if (!$token) {
            $this->error('El token de deploy no está configurado en el archivo .env (DEPLOY_TOKEN)');
            return 1;
        }

        if (empty($command)) {
            $this->error('El comando es requerido');
            return 1;
        }

        $this->info("Ejecutando comando remoto: {$command}");
        $this->info("URL: {$url}");

        try {
            $response = Http::withHeaders([
                'X-Deploy-Token' => $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ])->post($url, [
                'command' => urlencode($command),
            ]);

            if ($response->successful()) {
                $body = $response->body();

                // Verificar si la respuesta contiene una página HTML de Inertia con data-page
                if (strpos($body, 'data-page=') !== false) {
                    // Extraer el JSON del atributo data-page (soporta comillas simples o dobles)
                    if (preg_match('/data-page=([\'\"])(.*?)\1/s', $body, $matches)) {
                        $encoded = $matches[2];
                        $jsonData = html_entity_decode($encoded);
                        $errorData = json_decode($jsonData, true);

                        if (is_array($errorData) && isset($errorData['props'])) {
                            $props = $errorData['props'];
                            $this->error("Error del servidor:");
                            if (isset($props['codigo'])) $this->error("Código: {$props['codigo']}");
                            if (isset($props['titulo'])) $this->error("Título: {$props['titulo']}");
                            if (isset($props['mensaje'])) $this->error("Mensaje: {$props['mensaje']}");
                            // Mostrar props completos para debugging
                            $this->line(json_encode($props, JSON_PRETTY_PRINT));
                            return 1;
                        }
                    }
                }

                $data = $response->json();

                if ($data === null) {
                    $this->error("La respuesta no es JSON válido");
                    $this->error("Respuesta cruda: {$body}");
                    return 1;
                }

                // Mostrar toda la respuesta para debugging
                $this->info("Respuesta completa del servidor:");
                $this->line(json_encode($data, JSON_PRETTY_PRINT));

                if (isset($data['error'])) {
                    $this->error("Error: {$data['error']}");
                    if (isset($data['message'])) {
                        $this->error("Mensaje: {$data['message']}");
                    }
                    return 1;
                }

                $status = $data['status'] ?? 'Comando ejecutado';
                $output = $data['output'] ?? '';
                $exitCode = $data['exitCode'] ?? 0;

                $this->info("Estado: {$status}");
                $this->info("Código de salida: {$exitCode}");

                if ($output) {
                    $this->line("Salida del comando:");
                    $this->line($output);
                } else {
                    $this->warn("No hay salida del comando");
                }

                return $exitCode;
            } else {
                $this->error("Error HTTP: {$response->status()}");
                $this->error("Respuesta: {$response->body()}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("Error de conexión: {$e->getMessage()}");
            return 1;
        }
    }
}
