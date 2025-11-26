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
        // DEBUG: Log para detectar si Facebook llega al controlador
        /*$userAgent = request()->header('User-Agent', '');
        if (stripos($userAgent, 'facebook') !== false) {
            \Illuminate\Support\Facades\Log::channel('validation')->info('[EnlaceCortoController] Facebook bot reached controller', [
                'url' => request()->fullUrl(),
                'user_agent' => $userAgent,
                'prefijo' => $prefijo,
                'codigo' => $codigo,
            ]);
        }*/

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

        // Modo preview forzado para testing (útil para herramientas SEO)
        // Uso: /e/abc123?preview=1
        $forcePreview = request()->query('preview') == '1';

        // Detectar si es un bot de red social que necesita metadatos o modo preview forzado
        if ($this->esRedSocialCompartiendo() || $forcePreview) {
            // Log para debugging
            \Illuminate\Support\Facades\Log::info('Mostrando preview de enlace corto', [
                'url_corta' => request()->fullUrl(),
                'url_destino' => $enlace->url_original,
                'user_agent' => request()->header('User-Agent'),
                'prefijo' => $prefijo,
                'codigo' => $codigo,
                'tiene_titulo' => !empty($enlace->titulo),
                'tiene_og_imagen' => !empty($enlace->og_imagen),
                'es_bot_social' => $this->esRedSocialCompartiendo(),
                'preview_forzado' => $forcePreview,
            ]);

            // Mostrar página HTML estática con metadatos SEO
            $response = response()->view('enlaces-cortos.preview', [
                'enlace' => $enlace,
                'url_destino' => $enlace->url_original,
                'preview_mode' => $forcePreview,
            ]);

            // Headers para optimizar caché y reducir TTFB
            $response->header('Cache-Control', 'public, max-age=86400, s-maxage=604800, stale-while-revalidate=86400');
            $response->header('X-Content-Type-Options', 'nosniff');
            $response->header('X-Frame-Options', 'SAMEORIGIN');

            // IMPORTANTE: Evitar indexación en buscadores (esta página es solo para bots sociales)
            $response->header('X-Robots-Tag', 'noindex, nofollow');

            // ETag para validación de caché eficiente
            $etag = md5($enlace->id . $enlace->updated_at);
            $response->setEtag($etag);
            $response->setPublic();
            $response->setMaxAge(86400);

            // isNotModified() compara el ETag del request con el de la respuesta
            // Si coinciden, modifica la respuesta a 304 Not Modified automáticamente
            $response->isNotModified(request());

            return $response;
        }

        // Para usuarios normales y buscadores: redirección directa con noindex
        $redirectResponse = redirect($enlace->url_original, 301);

        // IMPORTANTE: Agregar X-Robots-Tag también en redirects para evitar indexación
        // Esto asegura que Google NO indexe la URL del enlace corto
        $redirectResponse->header('X-Robots-Tag', 'noindex, nofollow');

        return $redirectResponse;
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
     * Lista específica de redes sociales y mensajería
     * NO incluye herramientas SEO ni motores de búsqueda
     */
    private function esRedSocialCompartiendo(): bool
    {
        $userAgent = request()->header('User-Agent', '');

        // Lista específica de bots de redes sociales y mensajería
        // (NO incluye herramientas SEO como Semrush, Seobility, ni Google/Bing)
        $redesSocialesYMensajeria = [
            'facebookexternalhit',      // Facebook
            'facebookcatalog',          // Facebook Catalog
            'Facebot',                  // Facebook
            'Twitterbot',               // Twitter
            'LinkedInBot',              // LinkedIn
            'WhatsApp',                 // WhatsApp
            'TelegramBot',              // Telegram
            'Slackbot',                 // Slack
            'Discordbot',               // Discord
            'Pinterest',                // Pinterest
            'Pinterestbot',             // Pinterest
            'instagram',                // Instagram
            'SkypeUriPreview',          // Skype
            'Iframely',                 // iframely (preview service)
            'vkShare',                  // VKontakte
            'redditbot',                // Reddit
            'Tumblr',                   // Tumblr
            'Applebot',                 // Apple (iMessage previews)
        ];

        // Verificar si el user agent contiene alguno de los identificadores
        foreach ($redesSocialesYMensajeria as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return true;
            }
        }

        return false;
    }


}
