<?php

namespace App\Http\Controllers;

use App\Services\EnlaceCortoService;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Jaybizzle\CrawlerDetect\CrawlerDetect;

class EnlaceCortoController extends Controller
{
    protected EnlaceCortoService $enlaceService;

    public function __construct(EnlaceCortoService $enlaceService)
    {
        $this->enlaceService = $enlaceService;
    }

    /**
     * Redirigir desde enlace corto
     */
    public function redirigir(string $prefijo, string $codigo)
    {
        // Buscar el enlace en la base de datos para obtener metadatos
        $enlace = \App\Models\EnlaceCorto::where('prefijo', $prefijo)
            ->where('codigo', $codigo)
            ->where('activo', true)
            ->first();

        if (!$enlace) {
            abort(404, 'Enlace no encontrado o expirado');
        }

        // Actualizar contador de clics
        $this->enlaceService->actualizarContadorClics($prefijo, $codigo);

        // Trackear en Google Analytics
        $analyticsController = app(AnalyticsController::class);
        $analyticsController->trackEnlaceCorto($prefijo, $codigo, $enlace->url_original, request());

        // Detectar si es un bot de red social que necesita metadatos
        if ($this->esRedSocialCompartiendo()) {
            // Mostrar página HTML estática con metadatos SEO solo para bots
            return response()->view('enlaces-cortos.preview', [
                'enlace' => $enlace,
                'url_destino' => $enlace->url_original
            ])->header('Cache-Control', 'public, max-age=86400'); // Cache 24h para bots
        }        // Para usuarios normales, redirección directa
        return redirect($enlace->url_original, 301);
    }



    /**
     * Obtener enlace corto para una URL (crea si no existe)
     * Todo se calcula automáticamente en el backend: prefijo, SEO, etc.
     */
    public function obtener(Request $request): JsonResponse
    {
        $request->validate([
            'url' => 'required|url|max:2000'
        ]);

        $url = $request->url;
        $creado = false;
        $fueAcortada = false;

        $enlace = $this->enlaceService->obtenerEnlaceParaUrl($url, $fueAcortada );

        if (!$fueAcortada) {
            // URL no supera el umbral de longitud
            return response()->json([
                'url_corta' => $url,
                'fue_acortada' => false,
                'razon' => 'URL no supera el umbral de longitud'
            ]);
        }

        if (!$enlace) {
            // Error al crear enlace
            return response()->json([
                'url_corta' => $url,
                'fue_acortada' => false,
                'razon' => 'No se pudo crear el enlace corto',
            ], 500);
        }

        return response()->json([
            'url_corta' => $enlace->url_corta,
            // 'titulo' => $enlace->titulo,
            'fue_acortada' => true
        ]);
    }

    /**
     * Detectar si el request viene de un bot de red social compartiendo el enlace
     * Usa la librería jaybizzle/crawler-detect que se mantiene actualizada
     */
    private function esRedSocialCompartiendo(): bool
    {
        $crawlerDetect = new CrawlerDetect();

        // Detectar si es un crawler/bot (incluye redes sociales)
        if ($crawlerDetect->isCrawler()) {
            $userAgent = request()->header('User-Agent', '');

            // Filtrar específicamente bots de redes sociales y mensajería
            // (excluir motores de búsqueda como Google, Bing que no necesitan preview)
            $redesSociales = [
                'facebook',
                'twitter',
                'linkedin',
                'whatsapp',
                'telegram',
                'discord',
                'slack',
                'pinterest',
                'instagram',
                'tiktok',
                'snapchat',
                'reddit',
                'tumblr',
                'skype',
                'teams',
                'signal',
                'apple', // iMessage
                'preview',
                'unfurling',
                'linkpreview',
                'opengraph'
            ];

            // Verificar si el bot detectado es de una red social
            foreach ($redesSociales as $red) {
                if (stripos($userAgent, $red) !== false) {
                    return true;
                }
            }
        }

        return false;
    }


}
