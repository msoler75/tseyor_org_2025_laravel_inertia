<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckShareCommand extends Command
{
    /**
     * The name and signature of the console command.
     * - --samples: number of urls per content type (default 1)
     * - --save-to: optional path to save results (defaults to storage/share_results)
     * */
    protected $signature = 'check:share {--samples=1} {--save-to=} {--only=}';

    /**
     * The console command description.
     */
    protected $description = 'Comprobar sharing preview en varias redes sociales y guardar respuestas (usa curl/UA por red)';

    public function handle()
    {
        $samples = max(1, (int) $this->option('samples'));
        $saveToOption = $this->option('save-to');

        $baseSaveDir = $saveToOption ? rtrim($saveToOption, "\\/") : storage_path('share_results');

        if (!is_dir($baseSaveDir)) {
            if (!@mkdir($baseSaveDir, 0755, true)) {
                $this->error("No se pudo crear el directorio de resultados: {$baseSaveDir}");
                return 1;
            }
        }

        // Definir redes sociales y user-agents representativos (20)
        $networks = [
            'facebookexternalhit' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
            'Facebot' => 'Facebot',
            'Twitterbot' => 'Twitterbot/1.0',
            'LinkedInBot' => 'LinkedInBot/1.0 (+http://www.linkedin.com)',
            'WhatsApp' => 'WhatsApp/2.19.81',
            'TelegramBot' => 'TelegramBot (like TwitterBot)',
            'Slackbot' => 'Slackbot-LinkExpanding 1.0 (+https://api.slack.com/robots)',
            'Discordbot' => 'Discordbot/2.0',
            'Pinterestbot' => 'Pinterest/0.2 (+http://www.pinterest.com/bot.html)',
            'Instagram' => 'Instagram 155.0',
            'SkypeUriPreview' => 'SkypeUriPreview',
            'Iframely' => 'Iframely (+http://iframely.com)',
            'vkShare' => 'vkShare/1.0 (+http://vk.com/)',
            'redditbot' => 'redditbot/1.0',
            'Tumblr' => 'Tumblr',
            'Applebot' => 'Applebot/0.1',
            'TikTok' => 'TikTok 1.0',
            'TikTokBot' => 'TikTokBot/1.0',
            'Googlebot' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'Pinterest' => 'Pinterestbot',
        ];

        // Filtrar por --only (coma-separado)
        $onlyOpt = trim((string) $this->option('only'));
        if (!empty($onlyOpt)) {
            $onlyList = array_map('trim', explode(',', $onlyOpt));
            $networks = array_filter($networks, function ($k) use ($onlyList) {
                return in_array($k, $onlyList, true) || in_array(strtolower($k), array_map('strtolower', $onlyList), true);
            }, ARRAY_FILTER_USE_KEY);
            if (empty($networks)) {
                $this->error('No hay redes que coincidan con --only. Revisa los nombres.');
                return 1;
            }
        }

        $contents = ['libros', 'comunicados', 'blog', 'evento'];

        $stats = [
            'total' => 0,
            'per_code' => [],
            'per_network' => [],
            'saved_files' => [],
        ];

        $this->info("Iniciando comprobación: {$samples} url(s) por tipo x " . count($networks) . " redes = " . ($samples * count($contents) * count($networks)) . " peticiones");

        foreach ($networks as $networkName => $userAgent) {
            $stats['per_network'][$networkName] = ['total' => 0, 'per_code' => []];

            foreach ($contents as $content) {
                for ($i = 0; $i < $samples; $i++) {
                    // Obtener un id real a partir del modelo Eloquent correspondiente
                    $id = $this->getSampleIdForContent($content);
                    $url = "https://tseyor.org/{$content}/{$id}";

                    $this->line("-> [{$networkName}] GET {$url}");

                    // Construir headers reales para esta red
                    $headers = $this->getHeadersForNetwork($networkName, $userAgent);

                    $result = $this->fetchUrlWithUserAgent($url, $userAgent, 20, $headers);

                    $stats['total']++;
                    $stats['per_network'][$networkName]['total']++;

                    $code = $result['http_code'] ?? 0;
                    $stats['per_code'][$code] = ($stats['per_code'][$code] ?? 0) + 1;
                    $stats['per_network'][$networkName]['per_code'][$code] = ($stats['per_network'][$networkName]['per_code'][$code] ?? 0) + 1;

                    // Guardar la respuesta completa (headers + body)
                    $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $networkName);
                    $fileName = sprintf('%s_%s_%s_%s.html', date('Ymd_His'), $safeName, $content, $id);
                    $filePath = $baseSaveDir . DIRECTORY_SEPARATOR . $fileName;

                    $saved = @file_put_contents($filePath, $result['raw'] ?? "");
                    if ($saved === false) {
                        $this->warn("No se pudo guardar respuesta en: {$filePath}");
                    } else {
                        $stats['saved_files'][] = $filePath;
                    }

                    // Breve output de estado
                    $this->info("   Resultado: HTTP {$code} -> " . ($saved ? basename($filePath) : 'no-saved'));

                    // Log detallado en canal 'share'
                    try {
                        Log::channel('share')->info('share.check', [
                            'network' => $networkName,
                            'user_agent' => $userAgent,
                            'url' => $url,
                            'http_code' => $code,
                            'file' => $saved ? $filePath : null,
                            'error' => $result['error'] ?? null,
                            'info' => $result['info'] ?? null,
                        ]);
                    } catch (\Exception $e) {
                        $this->warn('No se pudo escribir en el log share: ' . $e->getMessage());
                    }

                    // Pequeña pausa para evitar generar demasiadas peticiones en rafaga
                    usleep(200000); // 200ms
                }
            }
        }

        // Mostrar resumen
        $this->line('');
        $this->info('Resumen de comprobación:');
        $this->line('Total peticiones: ' . $stats['total']);

        ksort($stats['per_code']);
        foreach ($stats['per_code'] as $code => $count) {
            $this->line("  HTTP {$code}: {$count}");
        }

        $this->line('');
        $this->info('Resumen por red:');
        foreach ($stats['per_network'] as $net => $data) {
            $this->line("- {$net}: {$data['total']} peticiones");
            ksort($data['per_code']);
            foreach ($data['per_code'] as $c => $n) {
                $this->line("    HTTP {$c}: {$n}");
            }
        }

        $this->line('');
        $this->info('Archivos guardados: ' . count($stats['saved_files']));
        if (!empty($stats['saved_files'])) {
            $this->line('Directorio: ' . $baseSaveDir);
            $this->line('Ejemplos (5 primeros):');
            foreach (array_slice($stats['saved_files'], 0, 5) as $f) {
                $this->line('  - ' . $f);
            }
        }

        $this->info('Comprobación finalizada.');

        return 0;
    }

    /**
     * Obtener un id de muestra válido para un tipo de contenido consultando Eloquent.
     * Si no se puede obtener del modelo, devuelve un id aleatorio pequeño como fallback.
     */
    protected function getSampleIdForContent(string $content): int
    {
        // Mapear slug de contenido a modelo Eloquent
        $map = [
            'libros' => \App\Models\Libro::class,
            'comunicados' => \App\Models\Comunicado::class,
            'blog' => \App\Models\Entrada::class,
            'evento' => \App\Models\Evento::class,
        ];

        $modelClass = $map[$content] ?? null;

        if ($modelClass && class_exists($modelClass)) {
            try {
                $max = $modelClass::max('id');
                if ($max && $max > 0) {
                    // Elegir un id aleatorio dentro del rango 1..max
                    return random_int(1, (int) $max);
                }
            } catch (\Exception $e) {
                $this->warn("No se pudo consultar el modelo {$modelClass}: " . $e->getMessage());
            }
        } else {
            $this->warn("No existe mapeo de modelo para contenido: {$content}");
        }

        // Fallback seguro
        return random_int(1, 5000);
    }

    /**
     * Devuelve un array de cabeceras (clave=>valor) para imitar mejor cada crawler.
     */
    protected function getHeadersForNetwork(string $networkName, string $userAgent): array
    {
        $common = [
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'es-ES,es;q=0.9,en;q=0.8',
            'Accept-Encoding' => 'gzip, deflate, br',
            'Connection' => 'keep-alive',
        ];

        // Ajustes por red
        switch (strtolower($networkName)) {
            case 'facebookexternalhit':
            case 'facebot':
                $common['Accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
                $common['Accept-Language'] = 'en-US,en;q=0.9';
                break;
            case 'twitterbot':
                $common['Accept'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
                $common['User-Agent'] = $userAgent;
                break;
            case 'linkedinbot':
                $common['Accept-Language'] = 'en-US';
                break;
            case 'whatsapp':
                $common['Accept'] = 'text/html';
                break;
            case 'telegrambot':
                $common['Accept'] = 'text/html';
                break;
            case 'instagram':
                $common['Accept-Language'] = 'en-US';
                break;
            case 'tiktok':
            case 'tiktokbot':
                $common['Accept'] = 'text/html';
                break;
            case 'googlebot':
                $common['Accept-Language'] = 'en-US';
                break;
            default:
                // dejar common
                break;
        }

        return $common;
    }

    /**
     * Realiza la petición HTTP usando cURL y forzando el User-Agent.
     * Devuelve array con keys: http_code, raw (headers+body), headers, body
     */
    protected function fetchUrlWithUserAgent(string $url, string $userAgent, int $timeout = 20, array $headers = []): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
        if (!empty($headers)) {
            $hdrs = [];
            foreach ($headers as $k => $v) {
                $hdrs[] = $k . ': ' . $v;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $hdrs);
        }
        // Permitir que libcurl acepte y descomprima automaticamente cualquier
        // encoding soportado (gzip, deflate, br). Con esto la respuesta 'body'
        // será texto descomprimido en lugar de datos binarios.
        curl_setopt($ch, CURLOPT_ENCODING, '');
        // incluir headers en la salida
        curl_setopt($ch, CURLOPT_HEADER, true);

        // Forzar IPv4 (opcional) - comentar si no se desea
        // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        // curl_close($ch);

        $httpCode = $info['http_code'] ?? 0;
        $headerSize = $info['header_size'] ?? 0;
        $headers = '';
        $body = '';
        if ($raw !== false) {
            $headers = $headerSize ? substr($raw, 0, $headerSize) : '';
            $body = $headerSize ? substr($raw, $headerSize) : $raw;
        }

        // Construir contenido a guardar: status-line + headers + body
        $statusLine = isset($info['protocol']) ? "HTTP/" . ($info['protocol'] / 10) : '';
        // Preferimos usar el header raw tal cual
        $saveRaw = "--REQUEST-URL: {$url}\n--USER-AGENT: {$userAgent}\n--HTTP-CODE: {$httpCode}\n\n" . ($headers ?? '') . "\n" . ($body ?? '');

        if ($raw === false) {
            $saveRaw = "--REQUEST-URL: {$url}\n--USER-AGENT: {$userAgent}\n--ERROR: {$error}\n";
        }

        return [
            'http_code' => $httpCode,
            'raw' => $saveRaw,
            'headers' => $headers,
            'body' => $body,
            'error' => $error,
            'info' => $info,
        ];
    }
}
