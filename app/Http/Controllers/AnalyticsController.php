<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AnalyticsController extends Controller
{
    /**
     * Recibir datos de analytics via sendBeacon al cerrar navegador
     */
    public function beacon(Request $request)
    {
        try {
            $data = $request->all();

            // Validar datos mínimos requeridos
            if (!isset($data['event']) || !isset($data['content_type'])) {
                return response()->json(['error' => 'Datos incompletos'], 400);
            }

            // Log para debugging (opcional)
            Log::info('Analytics Beacon received', [
                'event' => $data['event'],
                'content_type' => $data['content_type'],
                'content_title' => $data['content_title'] ?? null,
                'view_time_seconds' => $data['view_time_seconds'] ?? null,
                'time_category' => $data['time_category'] ?? null,
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
                'timestamp' => $data['timestamp'] ?? now()->timestamp
            ]);

            // Reenviar a Google Analytics via servidor
            $this->sendToGoogleAnalytics($data, $request);

            // Respuesta mínima y rápida (importante para beacon)
            return response('', 204); // 204 No Content

        } catch (\Exception $e) {
            Log::error('Analytics Beacon error', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ]);

            return response('', 204); // Siempre responder 204 para no bloquear
        }
    }

    /**
     * Enviar datos a Google Analytics via Measurement Protocol
     */
    private function sendToGoogleAnalytics($data, Request $request)
    {
        try {
            $measurementId = config('services.google_analytics.measurement_id');
            $apiSecret = config('services.google_analytics.api_secret');

            if (!$measurementId || !$apiSecret) {
                Log::warning('Google Analytics credentials not configured');
                return;
            }

            // Usar el client_id enviado desde el frontend, o generar uno como fallback
            $clientId = $data['client_id'] ?? $this->generateClientId($request);

            // Preparar payload para GA4 Measurement Protocol
            $payload = [
                'client_id' => $clientId,
                'events' => [
                    [
                        'name' => $data['event'],
                        'params' => [
                            'content_type' => $data['content_type'],
                            'content_title' => $data['content_title'] ?? '',
                            'view_time_seconds' => intval($data['view_time_seconds'] ?? 0),
                            'view_time_minutes' => floatval($data['view_time_minutes'] ?? 0),
                            'time_category' => $data['time_category'] ?? '',
                            'page_title' => $data['page_title'] ?? '',
                            'page_location' => $data['page_location'] ?? '',
                            'source' => 'server_beacon', // Identificar que viene del servidor
                            'engagement_time_msec' => intval(($data['view_time_seconds'] ?? 0) * 1000)
                        ]
                    ]
                ]
            ];

            // Enviar a Google Analytics
            $response = Http::timeout(5)->post(
                "https://www.google-analytics.com/mp/collect?measurement_id={$measurementId}&api_secret={$apiSecret}",
                $payload
            );

            if ($response->successful()) {
                Log::info('Data sent to Google Analytics via server', [
                    'event' => $data['event'],
                    'client_id' => $clientId,
                    'source' => $data['client_id'] ? 'frontend_client_id' : 'generated_client_id'
                ]);
            } else {
                Log::warning('Failed to send to Google Analytics', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error sending to Google Analytics', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
        }
    }

    /**
     * Generar un client_id consistente para el usuario
     * IMPORTANTE: Debe ser el mismo ID para el mismo usuario durante su sesión/día
     */
    private function generateClientId(Request $request)
    {
        // Usar datos que no cambien durante la sesión del usuario
        $userFingerprint = $request->ip() . '|' . $request->userAgent();

        // MD5 del fingerprint (consistente para el mismo usuario)
        $hashedFingerprint = md5($userFingerprint);

        // Usar solo fecha (no hora/minutos) para mantener consistencia durante el día
        $dateStamp = date('Ymd'); // Ej: 20250922

        // Formato de client_id de GA4: {random}.{timestamp}
        // Usamos hash consistente + timestamp del día
        return substr($hashedFingerprint, 0, 10) . '.' . strtotime($dateStamp);
    }
}
