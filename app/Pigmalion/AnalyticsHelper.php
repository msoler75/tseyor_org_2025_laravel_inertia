<?php

namespace App\Pigmalion;

use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsHelper
{
    /**
     * Trackear evento de enlace corto
     */
    public static function trackEnlaceCorto(string $prefijo, string $codigo, string $urlDestino, Request $request = null): void
    {
        try {
            $request = $request ?? request();

            $analyticsController = app(AnalyticsController::class);
            $analyticsController->trackEnlaceCorto($prefijo, $codigo, $urlDestino, $request);

        } catch (\Exception $e) {
            Log::error('Error en AnalyticsHelper::trackEnlaceCorto', [
                'error' => $e->getMessage(),
                'prefijo' => $prefijo,
                'codigo' => $codigo
            ]);
        }
    }

    /**
     * Trackear evento personalizado para enlaces cortos
     */
    public static function trackEventoPersonalizado(string $evento, array $parametros = [], Request $request = null): void
    {
        try {
            $measurementId = config('services.google_analytics.measurement_id');
            $apiSecret = config('services.google_analytics.api_secret');

            if (!$measurementId || !$apiSecret) {
                Log::warning('Google Analytics credentials not configured');
                return;
            }

            $request = $request ?? request();
            $clientId = self::generateClientId($request);

            // Preparar payload
            $payload = [
                'client_id' => $clientId,
                'events' => [
                    [
                        'name' => $evento,
                        'params' => array_merge([
                            'source' => 'enlaces_cortos_helper',
                            'method' => 'server_side'
                        ], $parametros)
                    ]
                ]
            ];

            // Enviar a Google Analytics
            $response = \Illuminate\Support\Facades\Http::timeout(3)->post(
                "https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}",
                $payload
            );

            if ($response->successful()) {
                Log::info('Evento personalizado enviado a GA', [
                    'evento' => $evento,
                    'parametros' => $parametros
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error enviando evento personalizado a GA', [
                'error' => $e->getMessage(),
                'evento' => $evento
            ]);
        }
    }

    /**
     * Generar client_id consistente (duplicado del AnalyticsController)
     */
    private static function generateClientId(Request $request): string
    {
        $userFingerprint = $request->ip() . '|' . $request->userAgent();
        $hashedFingerprint = md5($userFingerprint);
        $dateStamp = date('Ymd');

        return substr($hashedFingerprint, 0, 10) . '.' . strtotime($dateStamp);
    }

    /**
     * Trackear creación de enlace corto
     */
    public static function trackCreacionEnlace(string $prefijo, string $codigo, string $urlOriginal, bool $esNuevo = true): void
    {
        $evento = $esNuevo ? 'enlace_corto_creado' : 'enlace_corto_reutilizado';

        self::trackEventoPersonalizado($evento, [
            'enlace_codigo' => $codigo,
            'enlace_prefijo' => $prefijo,
            'enlace_corto' => "/{$prefijo}/{$codigo}",
            'url_original' => $urlOriginal,
            'longitud_original' => strlen($urlOriginal)
        ]);
    }

}
