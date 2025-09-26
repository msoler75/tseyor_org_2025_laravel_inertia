<?php

namespace App\Services;


use App\Models\EnlaceCorto;
use App\Pigmalion\AnalyticsHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class EnlaceCortoService
{
    /**
     * Crear un enlace corto
     */
    public function crear(
        string $urlOriginal,
        ?string $codigoPersonalizado = null,
        ?string $prefijo = null,
        ?string $titulo = null,
        ?string $descripcion = null,
        array $seoData = []
    ): EnlaceCorto {
        // Validar URL
        if (!filter_var($urlOriginal, FILTER_VALIDATE_URL)) {
            Log::error('[EnlaceCortoService] URL no válida', ['url' => $urlOriginal]);
            throw new \InvalidArgumentException('URL no válida');
        }

        // Detectar automáticamente el prefijo si no se proporciona
        if (!$prefijo) {
            $prefijo = $this->detectarPrefijo($urlOriginal);
            Log::info('[EnlaceCortoService] Prefijo detectado', ['url' => $urlOriginal, 'prefijo' => $prefijo]);
        }

        // Verificar si ya existe un enlace para esta URL
        $existing = EnlaceCorto::where('url_original', $urlOriginal)
            ->where('prefijo', $prefijo)
            ->where('activo', true)
            ->first();
        if ($existing) {
            Log::info('[EnlaceCortoService] Enlace existente encontrado', ['url' => $urlOriginal, 'prefijo' => $prefijo, 'codigo' => $existing->codigo]);
            return $existing;
        }

        // Generar código único
        $codigo = $codigoPersonalizado ?: $this->generarCodigoUnico($prefijo);
        Log::info('[EnlaceCortoService] Código generado', ['codigo' => $codigo, 'prefijo' => $prefijo]);

        // Intentar obtener metadatos y contenido relacionado
        $contenidoRelacionado = $this->buscarContenidoRelacionado($urlOriginal);
        $metadatos = null;

        if ($contenidoRelacionado) {
            $metadatos = $this->extraerMetadatosDesdeContenido($contenidoRelacionado);
        }

        // Si no se encontró en Contenido, extraer desde la URL
        if (!$metadatos) {
            $metadatos = $this->extraerMetadatosSEO($urlOriginal, $seoData);
        }        // Crear enlace
        $enlace = EnlaceCorto::create([
            'codigo' => $codigo,
            'url_original' => $urlOriginal,
            'prefijo' => $prefijo,
            'titulo' => mb_substr($titulo ?: ($metadatos['titulo'] ?? ''), 0, 255),
            'descripcion' => mb_substr($descripcion ?: ($metadatos['descripcion'] ?? ''), 0, 400),
            'contenido_id' => $contenidoRelacionado?->id,

            // Campos SEO
            'meta_titulo' => mb_substr($seoData['meta_titulo'] ?? ($metadatos['meta_titulo'] ?? ''), 0, 255),
            'meta_descripcion' => mb_substr($seoData['meta_descripcion'] ?? ($metadatos['meta_descripcion'] ?? ''), 0, 400),
            'meta_keywords' => mb_substr($seoData['meta_keywords'] ?? ($metadatos['meta_keywords'] ?? ''), 0, 255),
            'og_titulo' => mb_substr($seoData['og_titulo'] ?? ($metadatos['og_titulo'] ?? ''), 0, 255),
            'og_descripcion' => mb_substr($seoData['og_descripcion'] ?? ($metadatos['og_descripcion'] ?? ''), 0, 400),
            'og_imagen' => mb_substr($seoData['og_imagen'] ?? ($metadatos['og_imagen'] ?? ''), 0, 255),
            'og_tipo' => mb_substr($seoData['og_tipo'] ?? 'website', 0, 20),
            'twitter_card' => mb_substr($seoData['twitter_card'] ?? 'summary', 0, 20),
            'twitter_titulo' => mb_substr($seoData['twitter_titulo'] ?? ($metadatos['twitter_titulo'] ?? ''), 0, 255),
            'twitter_descripcion' => mb_substr($seoData['twitter_descripcion'] ?? ($metadatos['twitter_descripcion'] ?? ''), 0, 400),
            'twitter_imagen' => mb_substr($seoData['twitter_imagen'] ?? ($metadatos['twitter_imagen'] ?? ''), 0, 255),
            'canonical_url' => mb_substr($seoData['canonical_url'] ?? '', 0, 255),
        ]);

        // Trackear creación en Google Analytics
        try {
            AnalyticsHelper::trackCreacionEnlace($prefijo, $codigo, $urlOriginal, true);
        } catch (\Exception $e) {
            Log::warning('Error tracking enlace creation', ['error' => $e->getMessage()]);
        }

        // Log detallado del tipo de enlace creado
        $tipoEnlace = $this->determinarTipoEnlace($urlOriginal, $contenidoRelacionado);
        Log::info('Enlace corto creado', [
            'codigo' => $codigo,
            'prefijo' => $prefijo,
            'tipo' => $tipoEnlace,
            'tiene_contenido_relacionado' => $contenidoRelacionado !== null,
            'url_original' => $urlOriginal
        ]);

        Log::info('[EnlaceCortoService] Enlace creado', ['id' => $enlace->id, 'codigo' => $enlace->codigo, 'prefijo' => $enlace->prefijo, 'url_original' => $enlace->url_original, 'url_corta' => $enlace->url_corta ?? null]);
        return $enlace;
    }



    /**
     * Resolver un enlace corto y redirigir
     */
    public function resolver(string $prefijo, string $codigo): ?string
    {
        $cacheKey = "enlace_corto:{$prefijo}:{$codigo}";

        $enlace = Cache::remember($cacheKey, config('enlaces_cortos.ttl_cache', 60), function () use ($prefijo, $codigo) {
            return EnlaceCorto::where('prefijo', $prefijo)
                ->where('codigo', $codigo)
                ->activos()
                ->first();
        });

        if (!$enlace) {
            return null;
        }

        // Incrementar contador (async para no afectar rendimiento)
        dispatch(function () use ($enlace) {
            $enlace->incrementarClics();
        })->afterResponse();

        return $enlace->url_original;
    }

    /**
     * Generar código único
     */
    private function generarCodigoUnico(string $prefijo): string
    {
        $length = config('enlaces_cortos.codigo.longitud', 6);
        $characters = config('enlaces_cortos.codigo.caracteres');

        if (config('enlaces_cortos.codigo.excluir_similares', true)) {
            $characters = str_replace(['0', 'O', 'l', 'I', '1'], '', $characters);
        }

        do {
            $codigo = '';
            for ($i = 0; $i < $length; $i++) {
                $codigo .= $characters[random_int(0, strlen($characters) - 1)];
            }
        } while (EnlaceCorto::where('prefijo', $prefijo)->where('codigo', $codigo)->exists());

        return $codigo;
    }

    /**
     * Buscar contenido relacionado por URL (solo para URLs internas)
     */
    private function buscarContenidoRelacionado(string $url): ?\App\Models\Contenido
    {
        // Solo buscar contenido relacionado si es una URL interna
        if (!$this->esUrlInterna($url)) {
            return null;
        }

        // Solo buscar si no es un archivo directo
        if ($this->esArchivoDirecto($url)) {
            return null;
        }

        // Convertir URL completa a path relativo
        $path = parse_url($url, PHP_URL_PATH);

        // Buscar en el modelo Contenido
        return \App\Models\Contenido::where(function ($query) use ($path) {
            $query->whereRaw("CONCAT('/', coleccion, '/', COALESCE(slug_ref, id_ref)) = ?", [$path])
                ->orWhere('slug_ref', ltrim($path, '/'));
        })
            ->where('visibilidad', 'P')
            ->first();
    }

    /**
     * Verificar si una URL es interna al sitio
     */
    private function esUrlInterna(string $url): bool
    {
        $urlHost = parse_url($url, PHP_URL_HOST);
        $siteHost = parse_url(config('app.url'), PHP_URL_HOST);

        return $urlHost === $siteHost;
    }

    /**
     * Verificar si una URL apunta a un archivo directo
     */
    private function esArchivoDirecto(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH);

        // Extensiones de archivos que se consideran "directos"
        $extensionesArchivos = [
            'pdf',
            'doc',
            'docx',
            'ppt',
            'pptx',
            'xls',
            'xlsx',  // Documentos
            'mp3',
            'wav',
            'ogg',
            'm4a',
            'aac',
            'flac',
            'wma',    // Audio
            'mp4',
            'avi',
            'mov',
            'wmv',
            'flv',
            'webm',           // Video
            'jpg',
            'jpeg',
            'png',
            'gif',
            'svg',
            'webp',          // Imágenes
            'zip',
            'rar',
            '7z',
            'tar',
            'gz'                      // Archivos comprimidos
        ];

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, $extensionesArchivos);
    }

    /**
     * Generar metadatos básicos para archivos directos
     */
    private function generarMetadatosArchivo(string $url): array
    {
        $path = parse_url($url, PHP_URL_PATH);
        $nombreArchivo = basename($path);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $nombreSinExtension = pathinfo($nombreArchivo, PATHINFO_FILENAME);

        // Mapeo de tipos de archivo
        $tiposArchivo = [
            'pdf' => 'Documento PDF',
            'doc' => 'Documento Word',
            'docx' => 'Documento Word',
            'ppt' => 'Presentación PowerPoint',
            'pptx' => 'Presentación PowerPoint',
            'xls' => 'Hoja de cálculo Excel',
            'xlsx' => 'Hoja de cálculo Excel',
            'mp3' => 'Audio MP3',
            'wav' => 'Audio WAV',
            'ogg' => 'Audio OGG',
            'm4a' => 'Audio M4A',
            'aac' => 'Audio AAC',
            'flac' => 'Audio FLAC',
            'mp4' => 'Video MP4',
            'avi' => 'Video AVI',
            'mov' => 'Video MOV',
            'jpg' => 'Imagen JPEG',
            'jpeg' => 'Imagen JPEG',
            'png' => 'Imagen PNG',
            'gif' => 'Imagen GIF',
            'svg' => 'Imagen SVG',
            'webp' => 'Imagen WebP',
            'zip' => 'Archivo ZIP',
            'rar' => 'Archivo RAR',
            '7z' => 'Archivo 7Z'
        ];

        $tipoArchivo = $tiposArchivo[$extension] ?? 'Archivo ' . strtoupper($extension);
        $titulo = $nombreSinExtension ?: $nombreArchivo;
        $descripcion = "Descargar {$tipoArchivo}: {$nombreArchivo}";

        return [
            'titulo' => $titulo,
            'descripcion' => $descripcion,
            'meta_titulo' => substr($titulo, 0, 60),
            'meta_descripcion' => substr($descripcion, 0, 160),
            'og_titulo' => substr($titulo, 0, 95),
            'og_descripcion' => substr($descripcion, 0, 300),
            'og_tipo' => $this->obtenerTipoOgParaArchivo($extension),
            'twitter_titulo' => substr($titulo, 0, 70)
        ];
    }

    /**
     * Obtener tipo Open Graph apropiado para archivo
     */
    private function obtenerTipoOgParaArchivo(string $extension): string
    {
        $tiposOg = [
            'mp3' => 'music.song',
            'wav' => 'music.song',
            'ogg' => 'music.song',
            'm4a' => 'music.song',
            'aac' => 'music.song',
            'flac' => 'music.song',
            'mp4' => 'video.other',
            'avi' => 'video.other',
            'mov' => 'video.other',
            'jpg' => 'website',
            'jpeg' => 'website',
            'png' => 'website',
            'gif' => 'website',
            'svg' => 'website',
            'webp' => 'website'
        ];

        return $tiposOg[$extension] ?? 'website';
    }

    /**
     * Determinar el tipo de enlace para logging
     */
    private function determinarTipoEnlace(string $url, ?\App\Models\Contenido $contenido): string
    {
        if ($contenido) {
            return 'contenido_interno';
        }

        if ($this->esArchivoDirecto($url)) {
            return 'archivo_directo';
        }

        if ($this->esUrlInterna($url)) {
            return 'url_interna';
        }

        return 'url_externa';
    }

    /**
     * Extraer metadatos desde el modelo Contenido
     */
    private function extraerMetadatosDesdeContenido(\App\Models\Contenido $contenido): array
    {
        $metadatos = [
            'titulo' => $contenido->titulo,
            'descripcion' => $contenido->descripcion,
            'meta_titulo' => $contenido->titulo,
            'meta_descripcion' => substr($contenido->descripcion ?: '', 0, 160),
            'og_titulo' => substr($contenido->titulo ?: '', 0, 95),
            'og_descripcion' => substr($contenido->descripcion ?: '', 0, 300),
        ];

        // Si tiene imagen, agregarla
        if ($contenido->imagen) {
            $metadatos['og_imagen'] = url($contenido->imagen);
            $metadatos['twitter_imagen'] = url($contenido->imagen);
        }

        return $metadatos;
    }
    /**
     * Extraer metadatos SEO de una URL
     */
    private function extraerMetadatosSEO(string $url, array $seoDataExistente = []): array
    {
        $metadatos = [
            'titulo' => null,
            'descripcion' => null,
            'meta_titulo' => null,
            'meta_descripcion' => null,
            'meta_keywords' => null,
            'og_titulo' => null,
            'og_descripcion' => null,
            'og_imagen' => null,
            'twitter_titulo' => null,
            'twitter_descripcion' => null,
            'twitter_imagen' => null,
        ];

        // Si es un archivo directo, generar metadatos básicos
        if ($this->esArchivoDirecto($url)) {
            return $this->generarMetadatosArchivo($url);
        }

        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'Mozilla/5.0 (compatible; TSEYORBot/1.0)'
                ]
            ]);

            $html = @file_get_contents($url, false, $context);
            if (!$html) {
                $metadatos['titulo'] = parse_url($url, PHP_URL_HOST);
                return $metadatos;
            }

            // Título de la página
            if (preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $matches)) {
                $metadatos['titulo'] = trim(html_entity_decode($matches[1]));
            }

            // Meta description
            if (preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['descripcion'] = trim($matches[1]);
                $metadatos['meta_descripcion'] = substr(trim($matches[1]), 0, 160);
            }

            // Meta keywords
            if (preg_match('/<meta[^>]*name=["\']keywords["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['meta_keywords'] = trim($matches[1]);
            }

            // Open Graph data
            if (preg_match('/<meta[^>]*property=["\']og:title["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['og_titulo'] = substr(trim($matches[1]), 0, 95);
            }

            if (preg_match('/<meta[^>]*property=["\']og:description["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['og_descripcion'] = trim($matches[1]);
            }

            if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['og_imagen'] = trim($matches[1]);
            }

            // Twitter Card data
            if (preg_match('/<meta[^>]*name=["\']twitter:title["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['twitter_titulo'] = substr(trim($matches[1]), 0, 70);
            }

            if (preg_match('/<meta[^>]*name=["\']twitter:description["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['twitter_descripcion'] = trim($matches[1]);
            }

            if (preg_match('/<meta[^>]*name=["\']twitter:image["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches)) {
                $metadatos['twitter_imagen'] = trim($matches[1]);
            }
        } catch (\Exception $e) {
            Log::warning('Error extrayendo metadatos SEO', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
        }

        // Fallbacks
        if (!$metadatos['titulo']) {
            $metadatos['titulo'] = parse_url($url, PHP_URL_HOST);
        }

        $metadatos['meta_titulo'] = $metadatos['meta_titulo'] ?: substr($metadatos['titulo'], 0, 60);
        $metadatos['og_titulo'] = $metadatos['og_titulo'] ?: substr($metadatos['titulo'], 0, 95);
        $metadatos['twitter_titulo'] = $metadatos['twitter_titulo'] ?: substr($metadatos['titulo'], 0, 70);

        return $metadatos;
    }

    /**
     * Limpiar enlaces inactivos (solo si están marcados como inactivos manualmente)
     */
    public function limpiarInactivos(): int
    {
        $count = EnlaceCorto::where('activo', false)->delete();
        Log::info("Limpiados {$count} enlaces cortos inactivos");
        return $count;
    }



    /**
     * Validar dominio permitido
     */
    public function esDominioPermitido(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        $blockedDomains = config('enlaces_cortos.dominios_bloqueados', []);

        return !in_array($host, $blockedDomains);
    }

    /**
     * Generar código personalizado para eventos
     */
    public function generarCodigoEvento(string $nombre): string
    {
        $base = Str::slug($nombre, '');
        $base = substr($base, 0, 8);

        $sufijo = 1;
        $codigo = $base;

        while (EnlaceCorto::where('prefijo', 'e')->where('codigo', $codigo)->exists()) {
            $codigo = $base . $sufijo;
            $sufijo++;
        }

        return $codigo;
    }

    /**
     * Actualizar contador de clics y último acceso
     */
    public function actualizarContadorClics(string $prefijo, string $codigo): void
    {
        try {
            EnlaceCorto::where('prefijo', $prefijo)
                ->where('codigo', $codigo)
                ->where('activo', true)
                ->increment('clics');

            // Actualizar timestamp del último clic
            EnlaceCorto::where('prefijo', $prefijo)
                ->where('codigo', $codigo)
                ->update(['ultimo_clic' => now()]);
        } catch (\Exception $e) {
            Log::warning('Error actualizando contador de clics', [
                'prefijo' => $prefijo,
                'codigo' => $codigo,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener enlace corto para una URL con toda la lógica de negocio
     * Busca existente o crea nuevo, maneja umbral de longitud, tracking, etc.
     *
     * @param string $url URL original
     * @param bool|null $creado Parámetro por referencia que indica si se creó un nuevo enlace
     * @param bool|null $fueAcortada Parámetro por referencia que indica si la URL fue acortada
     * @return EnlaceCorto|null Modelo del enlace corto o null si no se pudo crear/acortar
     */
    public function obtenerEnlaceParaUrl(string $url,  &$fueAcortada = null, &$existia = null): ?EnlaceCorto
    {
        $existia = false;
        $fueAcortada = false;

        Log::info('[EnlaceCortoService] obtenerEnlaceParaUrl llamada', ['url' => $url]);
        // Buscar enlace existente
        $enlace = EnlaceCorto::where('url_original', $url)
            ->where('activo', true)
            ->first();

        if ($enlace) {
            Log::info('[EnlaceCortoService] Enlace existente reutilizado', ['url' => $url, 'codigo' => $enlace->codigo, 'prefijo' => $enlace->prefijo, 'url_corta' => $enlace->url_corta ?? null]);
            // Trackear reutilización de enlace existente
            try {
                AnalyticsHelper::trackCreacionEnlace($enlace->prefijo, $enlace->codigo, $url, false);
            } catch (\Exception $e) {
                Log::warning('Error tracking enlace reuse', ['error' => $e->getMessage()]);
            }

            $fueAcortada = true;
            $existia = true;
            return $enlace;
        }

        // Verificar umbral de longitud SOLO del PATH
        $umbral = config('enlaces_cortos.umbral_longitud_auto', 20);
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $longitudPath = strlen($path);
        Log::info('[EnlaceCortoService] DEBUG umbral', [
            'url' => $url,
            'path' => $path,
            'longitud_path' => $longitudPath,
            'umbral' => $umbral,
            'comparacion' => $longitudPath . ' < ' . $umbral . ' = ' . ($longitudPath < $umbral ? 'true' : 'false')
        ]);
        if ($longitudPath < $umbral) {
            Log::info('[EnlaceCortoService] URL no supera umbral de longitud', ['url' => $url, 'longitud' => $longitudPath, 'umbral' => $umbral]);
            // No se acorta, devolver null (la URL original se usará)
            return null;
        }

        // Crear nuevo enlace corto
        try {
            $enlace = $this->crear($url);
            Log::info('[EnlaceCortoService] Nuevo enlace creado', ['url' => $url, 'codigo' => $enlace->codigo, 'prefijo' => $enlace->prefijo, 'url_corta' => $enlace->url_corta ?? null]);
            $fueAcortada = true;
            $existia = false;
            return $enlace;
        } catch (\Exception $e) {
            Log::error('[EnlaceCortoService] Error creando enlace corto automático', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * Crear o buscar enlace corto existente para una URL y modelo
     * Usado por ContenidoHelper
     */
    public static function crearOBuscarEnlaceCorto(string $url): ?EnlaceCorto
    {
        $service = app(self::class);

        // Buscar enlace existente
        $enlace = EnlaceCorto::where('url_original', $url)
            ->where('activo', true)
            ->first();

        if ($enlace) {
            return $enlace;
        }

        // Crear nuevo enlace
        try {
            return $service->crear($url);
        } catch (\Exception $e) {
            Log::warning('Error creando enlace corto en ContenidoHelper', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
    /**
     * Detectar prefijo automáticamente basado en la URL
     */
    private function detectarPrefijo(string $url): string
    {
        // Detectar por extensión de archivo
        $esDocumento = preg_match('/\.(pdf|docx?|pptx?|xlsx?)(\?|$)/i', $url);
        $esAudio = preg_match('/\.(mp3|wav|ogg|m4a|aac|flac|wma)(\?|$)/i', $url);

        if ($esDocumento) {
            return 'd'; // documentos
        } elseif ($esAudio) {
            return 'a'; // audio
        } else {
            return 'e'; // enlaces generales
        }
    }


}
