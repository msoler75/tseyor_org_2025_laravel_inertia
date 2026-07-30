<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenRouterService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://openrouter.ai/api/v1';

    public function __construct()
    {
        $this->apiKey = config('services.openrouter_key', '');
    }

    public function analizarEmailMantenimiento(string $rawEmailText): array
    {
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OPENROUTER_API_KEY no configurada. Añádela en el archivo .env');
        }

        $prompt = <<<PROMPT
Eres un asistente que extrae informacion estructurada de correos de DreamHost sobre mantenimiento programado de servidores.

Analiza el siguiente correo y extrae estos datos en formato JSON:

- titulo: Titulo corto en español para el anuncio, que incluya la fecha y la duracion de la caida.
  Ejemplo bueno: "Mantenimiento del servidor el 5 de agosto (caida maxima de 1 hora)"
  Ejemplo bueno: "Actualizacion del servidor el 12 de septiembre (~2 horas de interrupcion)"
  IMPORTANTE: Menciona SIEMPRE la duracion estimada de la caida en el titulo.

- descripcion: 2-3 frases en español, claras para un usuario no tecnico, que resuman:
  1. Que el sitio web estara temporalmente fuera de linea durante un maximo de X tiempo
  2. En que franja horaria aproximada puede ocurrir (convertida a hora española UTC+2 si es verano)
  3. Que los datos, archivos y configuraciones no se veran afectados
  Ejemplo: "El sitio web estara temporalmente fuera de linea durante un maximo de 1 hora, en algun momento entre las 09:00 y las 15:00 (hora española). Tus datos y configuraciones no se veran afectados."

- inicio: Fecha y hora de inicio de la ventana de mantenimiento en formato ISO 8601 UTC (YYYY-MM-DDTHH:MM:SSZ)

- fin: Fecha y hora de fin de la ventana de mantenimiento en formato ISO 8601 UTC (YYYY-MM-DDTHH:MM:SSZ).
  Calcula: hora_fin_de_ventana = hora_inicio_de_ventana + duracion_de_la_ventana (normalmente 6h).
  La duracion de la caida real es menor que la ventana.

- zona_horaria_original: La zona horaria mencionada en el correo (ej: "America/Los_Angeles")

- duracion_estimada: Duracion estimada de la caida real extraida del correo (ej: "1 hora", "aproximadamente 2 horas")

Reglas para las fechas:
- Convierte la fecha y hora del correo a UTC.
- PT (Pacific Time) en verano (marzo-noviembre) = UTC-7, en invierno = UTC-8.
- Si el correo dice "Entre 12:00 a.m. y 6:00 a.m. PT", inicio = medianoche PT convertida a UTC, fin = 6am PT convertida a UTC.
- Si el correo menciona una duracion de caida (ej: "1 hora"), usala para la descripcion pero NO cambies la ventana de inicio/fin.

Responde UNICAMENTE con el JSON, sin markdown, sin explicaciones ni texto adicional.

Correo:
$rawEmailText
PROMPT;

        try {
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('app.url', 'https://tseyor.org'),
                'X-Title' => 'Tseyor Maintenance Parser',
            ])->post($this->baseUrl . '/chat/completions', [
                'model' => config('services.openrouter_model', 'openai/gpt-4o-mini'),
                'messages' => [
                    ['role' => 'system', 'content' => 'Eres un parser que extrae datos estructurados de correos de DreamHost. Responde SOLO con JSON válido, sin markdown ni explicaciones.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0,
                'max_tokens' => 500,
            ]);

            if (!$response->successful()) {
                Log::error('[OpenRouter] Error en la API', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \RuntimeException('Error al llamar a OpenRouter: ' . $response->status());
            }

            $data = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? '';

            $content = trim($content);
            $content = preg_replace('/^```(?:json)?\s*|\s*```$/', '', $content);

            $parsed = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('[OpenRouter] JSON inválido de OpenRouter', ['content' => $content]);
                throw new \RuntimeException('OpenRouter devolvió JSON inválido: ' . json_last_error_msg());
            }

            return array_merge([
                'titulo' => '',
                'descripcion' => '',
                'inicio' => null,
                'fin' => null,
                'zona_horaria_original' => null,
                'duracion_estimada' => '',
            ], $parsed);

        } catch (\Exception $e) {
            Log::error('[OpenRouter] Excepción', ['message' => $e->getMessage()]);
            throw $e;
        }
    }
}
