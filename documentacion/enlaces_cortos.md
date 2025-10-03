# Sistema de Enlaces Cortos - TSEYOR.org

El sistema de enlaces cortos de TSEYOR permite crear URLs cortas y amigables para compartir contenido, con funcionalidades avanzadas de SEO, detección inteligente de bots sociales y estadísticas integradas con Google Analytics.

## 📋 Índice

- [Características Principales](#características-principales)
- [Configuración](#configuración)
- [Estructura de URLs](#estructura-de-urls)
- [Creación Bajo Demanda](#creación-bajo-demanda)
- [API](#api)
- [Frontend (Vue.js)](#frontend-vuejs)
- [Detección de Bots Sociales](#detección-de-bots-sociales)
- [SEO y Metadatos](#seo-y-metadatos)
- [Estadísticas](#estadísticas)
- [Arquitectura Técnica](#arquitectura-técnica)
- [Testing y Validación](#testing-y-validación)
- [Administración](#administración)
- [Ejemplos](#ejemplos)
- [Solución de Problemas](#solución-de-problemas)

## 🚀 Características Principales

- **Creación bajo demanda**: Enlaces generados solo cuando el usuario comparte (no automáticamente)
- **Detección inteligente de bots**: Lista específica de bots de redes sociales (Facebook, WhatsApp, Twitter, LinkedIn, etc.)
- **Vista previa SEO**: Página HTML con metadatos completos para bots sociales
- **Modo preview**: Parámetro `?preview=1` para testing y herramientas SEO
- **Enlaces permanentes**: Los enlaces cortos nunca expiran
- **SEO optimizado**: Metadatos específicos para cada red social (Open Graph, Twitter Cards)
- **Prefijos semánticos**: Diferentes prefijos según el tipo de contenido (e/d/a)
- **Umbral inteligente**: Solo se acortan URLs que superan criterios de longitud configurables
- **Estadísticas**: Integración con Google Analytics Measurement Protocol
- **Metadatos automáticos**: Extracción desde contenido relacionado o scraping de URLs externas
- **Rate limiting**: Protección contra abuso

## ⚙️ Configuración

### Variables de Entorno

Agrega estas variables a tu archivo `.env`:

```env
# Enlaces cortos
ENLACES_CORTOS_HABILITADO=true
ENLACES_CORTOS_DOMINIO=https://tseyor.org
ENLACES_CORTOS_UMBRAL_AUTO=80
ENLACES_CORTOS_LONGITUD_CODIGO=6
ENLACES_CORTOS_CARACTERES=abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789
ENLACES_CORTOS_ESTADISTICAS=true
ENLACES_CORTOS_TRACK_USER_AGENT=true
ENLACES_CORTOS_TRACK_REFERER=true
ENLACES_CORTOS_CACHE_TTL=60
ENLACES_CORTOS_MAX_POR_HORA=100
ENLACES_CORTOS_MAX_POR_DIA=500
```

### Archivo de Configuración

El archivo `config/enlaces_cortos.php` (en español) contiene toda la configuración del sistema:

```php
// Dominios principales y alternativos
'dominios' => [
    'principal' => env('APP_URL', 'http://localhost'),
    'alternativos' => [
        // 'tsy.es',     // Dominio corto oficial
        // 'amor.es',    // Dominio temático
        // 'luz.info',   // Dominio espiritual
    ]
],

// Prefijos por tipo de contenido (detección automática)
'prefijos' => [
    'por_defecto' => 'e',   // /e/abc123 (enlaces generales)
    'documentos' => 'd',    // /d/abc123 (documentos PDF, DOCX, etc.)
    'audio' => 'a',         // /a/abc123 (archivos de audio MP3, WAV, etc.)
],

// Configuración de códigos
'codigo' => [
    'longitud' => 6,
    'caracteres' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
    'excluir_similares' => true, // Excluye 0, O, l, I, 1
],

// Umbral para creación automática
'umbral_longitud_auto' => 80,

// Estadísticas con Google Analytics
'estadisticas' => [
    'habilitadas' => true,
    'google_analytics' => [
        'measurement_id' => env('GA_MEASUREMENT_ID'),
        'api_secret' => env('GA_API_SECRET'),
    ],
],
```

## 🔗 Estructura de URLs

### Patrones de URLs Cortas

| Prefijo | Uso | Ejemplo | URL Completa |
|---------|-----|---------|--------------|
| `e` | General (eventos, noticias, etc.) | `encuentro2024` | `https://tseyor.org/e/encuentro2024` |
| `d` | Documentos (comunicados, PDFs, etc.) | `comunicado42` | `https://tseyor.org/d/comunicado42` |
| `a` | Audio (MP3, WAV, etc.) | `radio240923` | `https://tseyor.org/a/radio240923` |

### Generación de Códigos

- **Longitud**: 6 caracteres por defecto
- **Caracteres**: Letras (mayúsculas/minúsculas) y números
- **Exclusiones**: Se excluyen caracteres similares (0, O, l, I, 1)
- **Únicos**: Cada código es único por prefijo

## 🤖 Creación Bajo Demanda

### En Share.vue (Modal de Compartir)

El componente `Share.vue` crea enlaces cortos bajo demanda cuando el usuario abre el modal de compartir:

```vue
<script setup>
const { acortarParaCompartir } = useEnlacesCortos()

const toggleSocialShow = () => {
    socialShow.value = true;
    // Crear enlace corto en segundo plano mientras el usuario elige red social
    prepararEnlaceCorto();
}

const prepararEnlaceCorto = async () => {
    // Verificar si ya existe un enlace corto
    const urlCorta = await acortarParaCompartir(fullUrl)
    if (urlCorta !== fullUrl) {
        currentUrl.value = urlCorta // Actualizar dinámicamente
    }
}
</script>
```

### Una Sola Llamada a API

El backend se encarga de todo el proceso en una sola llamada:

```javascript
// Composable useEnlacesCortos
const acortarParaCompartir = async (url) => {
    // Una sola función unificada que:
    // 1. Busca si ya existe el enlace
    // 2. Si existe, lo devuelve
    // 3. Si no existe y cumple umbral, lo crea
    // 4. Si no cumple umbral, devuelve URL original
    const urlCorta = await obtenerEnlace(url)
    return urlCorta
}
```

### Umbral de Longitud

URLs mayores a 80 caracteres (configurable) se acortan automáticamente al compartir.

## 📡 API

### Endpoints Disponibles

#### ⭐ Obtener Enlace Corto (Principal - Ultra Simplificado)
```http
POST /obtener-enlace
Content-Type: application/json

{
    "url": "https://example.com/very/long/url"
}
```

**Respuesta si se crea/encuentra enlace:**
```json
{
    "url_corta": "https://tseyor.org/e/xyz789",
    "fue_acortada": true
}
```

**Respuesta si no supera umbral de longitud:**
```json
{
    "url_corta": "https://example.com/short-url", 
    "fue_acortada": false,
    "razon": "URL no supera el umbral de longitud"
}
```

#### Redirección (Automática)
```http
GET /{prefijo}/{codigo}
# Ejemplos:
# GET /e/xyz789  -> 301 redirect para usuarios
# GET /e/xyz789  -> 200 HTML con metadatos para bots sociales
## 🔄 Flujo de Funcionamiento Completo

### 1. Creación Bajo Demanda (Share.vue)

El componente `Share.vue` crea enlaces cortos **bajo demanda** cuando el usuario abre el modal de compartir:

```vue
<script setup>
const { acortarParaCompartir } = useEnlacesCortos()

const toggleSocialShow = () => {
    socialShow.value = true;
    // Crear enlace corto en segundo plano mientras el usuario elige red social
    prepararEnlaceCorto();
}

const prepararEnlaceCorto = async () => {
    const urlCorta = await acortarParaCompartir(fullUrl)
    if (urlCorta !== fullUrl) {
        currentUrl.value = urlCorta // Actualizar dinámicamente
    }
}
</script>
```

**Ventajas**:
- No se crean enlaces innecesarios para contenido que nunca se comparte
- Creación transparente en segundo plano mientras el usuario decide
- Una sola llamada a API unificada que maneja todo

### 2. Detección Específica de Bots Sociales

El sistema usa una **lista personalizada** de bots de redes sociales (NO una librería genérica):

```php
private function esRedSocialCompartiendo(): bool
{
    $userAgent = request()->header('User-Agent', '');
    
    $redesSocialesYMensajeria = [
        'facebookexternalhit',  // Facebook
        'Facebot',              // Facebook
        'Twitterbot',           // Twitter
        'LinkedInBot',          // LinkedIn
        'WhatsApp',             // WhatsApp
        'TelegramBot',          // Telegram
        'Slackbot',             // Slack
        'Discordbot',           // Discord
        'Pinterest',            // Pinterest
        'instagram',            // Instagram
        'SkypeUriPreview',      // Skype
        'Applebot',             // Apple (iMessage)
        // ... más bots
    ];
    
    foreach ($redesSocialesYMensajeria as $bot) {
        if (stripos($userAgent, $bot) !== false) {
            return true;
        }
    }
    return false;
}
```

**Importante**: NO incluye herramientas SEO (Semrush, Seobility), ni motores de búsqueda (Google, Bing).

### 3. Vista Preview para Bots Sociales

Cuando se detecta un bot social (o modo `?preview=1`), se muestra `preview.blade.php`:

**Características**:
- HTML estático con metadatos completos
- Cache de 24 horas (`Cache-Control: public, max-age=86400`)
- Banner informativo en modo `?preview=1`
- Imagen Open Graph visible

### 4. Redirección para Usuarios Normales

```php
public function redirigir(string $prefijo, string $codigo)
{
    $enlace = EnlaceCorto::where('prefijo', $prefijo)
        ->where('codigo', $codigo)
        ->where('activo', true)
        ->first();
    
    if (!$enlace) {
        abort(404, 'Enlace no encontrado');
    }
    
    // Actualizar contador de clics
    $this->enlaceService->actualizarContadorClics($prefijo, $codigo);
    
    // Trackear en Google Analytics
    $analyticsController->trackEnlaceCorto($prefijo, $codigo, $enlace->url_original, request());
    
    // Detectar bot social o modo preview
    if ($this->esRedSocialCompartiendo() || request()->query('preview') == '1') {
        return response()->view('enlaces-cortos.preview', [
            'enlace' => $enlace,
            'url_destino' => $enlace->url_original,
            'preview_mode' => request()->query('preview') == '1',
        ])->header('Cache-Control', 'public, max-age=86400');
    }
    
    // Usuario normal: redirección 301
    return redirect($enlace->url_original, 301);
}
```

### 5. Umbral Inteligente de Acortamiento

El servicio evalúa múltiples criterios antes de acortar:

```php
public function necesitaAcortarse(string $url): bool
{
    $path = parse_url($url, PHP_URL_PATH) ?? '';
    $longitudUrl = strlen($url);
    $longitudPath = strlen($path);
    
    // Regla 1: URLs muy cortas nunca se acortan (<= 60 caracteres)
    if ($longitudUrl <= 60) return false;
    
    // Regla 2: Patrones excluidos (configurables)
    foreach (config('enlaces_cortos.patrones_excluidos', []) as $patron) {
        if (preg_match('/' . $patron . '/', $path)) return false;
    }
    
    // Regla 3: Path corto (<= 30 caracteres)
    if ($longitudPath <= 30) return false;
    
    // Regla 4: URLs con parámetros GET largos (> 20 caracteres)
    $query = parse_url($url, PHP_URL_QUERY);
    if ($query && strlen($query) > 20) return true;
    
    // Regla 5: URLs muy largas (> 80 caracteres)
    return $longitudUrl > 80;
}
```

### 6. Arquitectura DRY (Don't Repeat Yourself)

- **EnlaceCortoController**: Capa HTTP ultra-simplificada (161 líneas)
- **EnlaceCortoService**: Lógica de negocio centralizada (708 líneas)
- **preview.blade.php**: Vista específica para bots con metadatos SEO
- **Handler.php**: Método `esBotRedSocial()` reutilizable en toda la app

#### Obtener Enlace Corto (Endpoint Principal)
```http
POST /obtener-enlace
Content-Type: application/json

{
    "url": "https://example.com/very/long/url"
}
```

## 🤖 Detección de Bots Sociales

### Lista Específica vs Librería Genérica

El sistema **NO usa** librerías genéricas de detección de crawlers (como `jaybizzle/crawler-detect`). En su lugar, mantiene una **lista específica** de bots de redes sociales y mensajería.

**Razón**: Las librerías genéricas detectan TODOS los crawlers (incluyendo Google, Bing, herramientas SEO como Semrush, Seobility, etc.), lo que causaría que estas herramientas vean el HTML de preview en lugar de la redirección real.

### Implementación en EnlaceCortoController

```php
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
```

### Implementación Duplicada en Handler.php

El mismo método existe en `app/Exceptions/Handler.php` para manejar excepciones (errores 404, 500, etc.) de forma diferente para bots sociales:

```php
// app/Exceptions/Handler.php

/**
 * Detectar si el request viene de un bot de red social o mensajería
 * NO incluye herramientas SEO, motores de búsqueda ni otros crawlers
 */
protected function esBotRedSocial($request): bool
{
    $userAgent = $request->header('User-Agent', '');

    // Lista específica de bots de redes sociales y mensajería
    $redesSocialesYMensajeria = [
        'facebookexternalhit',
        'facebookcatalog',
        'Facebot',
        'Twitterbot',
        'LinkedInBot',
        'WhatsApp',
        'TelegramBot',
        'Slackbot',
        'Discordbot',
        'Pinterest',
        'Pinterestbot',
        'instagram',
        'SkypeUriPreview',
        'Iframely',
        'vkShare',
        'redditbot',
        'Tumblr',
        'Applebot',
        'developers.google.com/+/web/snippet', // Google+ (deprecado)
    ];

    foreach ($redesSocialesYMensajeria as $bot) {
        if (stripos($userAgent, $bot) !== false) {
            return true;
        }
    }

    return false;
}
```

### Bots Incluidos vs Excluidos

#### ✅ Incluidos (Muestran Preview HTML)
- **Redes Sociales**: Facebook, Twitter, LinkedIn, Instagram
- **Mensajería**: WhatsApp, Telegram, Slack, Discord, Skype
- **Otros**: Pinterest, Reddit, Tumblr, VK, Apple (iMessage)
- **Servicios de Preview**: Iframely

#### ❌ Excluidos (Reciben Redirección 301)
- **Motores de Búsqueda**: Google, Bing, Yahoo, DuckDuckGo
- **Herramientas SEO**: Semrush, Ahrefs, Moz, Seobility, Screaming Frog
- **Crawlers Genéricos**: Cualquier otro crawler no específico de redes sociales

### Modo Preview Forzado

Para testing y herramientas SEO, se puede forzar el modo preview con el parámetro `?preview=1`:

```php
public function redirigir(string $prefijo, string $codigo)
{
    // ...
    
    // Modo preview forzado para testing (útil para herramientas SEO)
    // Uso: /e/abc123?preview=1
    $forcePreview = request()->query('preview') == '1';
    
    // Detectar si es un bot de red social o modo preview forzado
    if ($this->esRedSocialCompartiendo() || $forcePreview) {
        return response()->view('enlaces-cortos.preview', [
            'enlace' => $enlace,
            'url_destino' => $enlace->url_original,
            'preview_mode' => $forcePreview,
        ])->header('Cache-Control', 'public, max-age=86400');
    }
    
    // Usuario normal: redirección 301
    return redirect($enlace->url_original, 301);
}
```

### User Agents de Ejemplo

```
# Facebook
facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)

# WhatsApp
WhatsApp/2.23.2.72 A

# Twitter
Twitterbot/1.0

# LinkedIn
LinkedInBot/1.0 (compatible; Mozilla/5.0; Apache-HttpClient +http://www.linkedin.com)

# Telegram
TelegramBot (like TwitterBot)

# Herramienta SEO (NO incluido - recibe redirección 301)
SEMrushBot/7~bl;+http://www.semrush.com/bot.html
```

### Logging de Detección

```php
Log::info('Mostrando preview de enlace corto', [
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
```

## 🎯 SEO y Metadatos Sociales

### Extracción Automática de Metadatos

El sistema extrae metadatos de **tres fuentes**, en orden de prioridad:

#### 1. Contenido Relacionado (Prioridad Alta)

Si la URL es interna y apunta a un `Contenido` en la base de datos:

```php
private function extraerMetadatosDesdeContenido(\App\Models\Contenido $contenido): array
{
    return [
        'titulo' => $contenido->titulo,
        'descripcion' => $contenido->descripcion,
        'meta_titulo' => $contenido->titulo,
        'meta_descripcion' => substr($contenido->descripcion ?: '', 0, 160),
        'og_titulo' => substr($contenido->titulo ?: '', 0, 95),
        'og_descripcion' => substr($contenido->descripcion ?: '', 0, 300),
        'og_imagen' => url($contenido->imagen),
        'twitter_imagen' => url($contenido->imagen),
    ];
}
```

#### 2. Archivos Directos (Documentos/Audio)

Para URLs que apuntan a archivos (PDF, MP3, etc.):

```php
private function generarMetadatosArchivo(string $url): array
{
    $nombreArchivo = basename($url);
    $extension = strtolower(pathinfo($url, PATHINFO_EXTENSION));
    
    $tiposArchivo = [
        'pdf' => 'Documento PDF',
        'mp3' => 'Audio MP3',
        'docx' => 'Documento Word',
        // ... más tipos
    ];
    
    $tipoArchivo = $tiposArchivo[$extension] ?? 'Archivo ' . strtoupper($extension);
    
    return [
        'titulo' => pathinfo($nombreArchivo, PATHINFO_FILENAME),
        'descripcion' => "Descargar {$tipoArchivo}: {$nombreArchivo}",
        'og_tipo' => $this->obtenerTipoOgParaArchivo($extension),
        // ... más metadatos
    ];
}
```

#### 3. Scraping de URL Externa (Fallback)

Para URLs externas o sin contenido relacionado:

```php
private function extraerMetadatosSEO(string $url): array
{
    $html = @file_get_contents($url, false, stream_context_create([
        'http' => [
            'timeout' => 5,
            'user_agent' => 'Mozilla/5.0 (compatible; TSEYORBot/1.0)'
        ]
    ]));
    
    // Extraer título
    preg_match('/<title[^>]*>(.*?)<\/title>/si', $html, $matches);
    $titulo = trim(html_entity_decode($matches[1]));
    
    // Extraer meta description
    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches);
    $descripcion = trim($matches[1]);
    
    // Extraer Open Graph
    preg_match('/<meta[^>]*property=["\']og:title["\'][^>]*content=["\']([^"\']*)["\'][^>]*>/i', $html, $matches);
    $ogTitulo = trim($matches[1]);
    
    // ... más extracciones
    
    return [
        'titulo' => $titulo,
        'descripcion' => $descripcion,
        'og_titulo' => $ogTitulo,
        // ... más metadatos
    ];
}
```

### Vista Preview con Metadatos Completos

La vista `preview.blade.php` incluye todos los metadatos necesarios para redes sociales:

```blade
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- SEO Básico -->
    <title>{{ $enlace->titulo ?: $enlace->url_original }}</title>
    <meta name="description" content="{{ $enlace->descripcion }}">
    <meta name="keywords" content="{{ $enlace->meta_keywords }}">
    
    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $enlace->og_tipo ?: 'website' }}">
    <meta property="og:url" content="{{ request()->url() }}">
    <meta property="og:title" content="{{ $enlace->og_titulo ?: $enlace->titulo }}">
    <meta property="og:description" content="{{ $enlace->og_descripcion ?: $enlace->descripcion }}">
    @if($enlace->og_imagen)
        <meta property="og:image" content="{{ $enlace->og_imagen }}">
    @endif
    
    <!-- Twitter Cards -->
    <meta property="twitter:card" content="{{ $enlace->twitter_card ?: 'summary' }}">
    <meta property="twitter:title" content="{{ $enlace->twitter_titulo ?: $enlace->titulo }}">
    <meta property="twitter:description" content="{{ $enlace->twitter_descripcion ?: $enlace->descripcion }}">
    @if($enlace->twitter_imagen)
        <meta property="twitter:image" content="{{ $enlace->twitter_imagen }}">
    @endif
    
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $enlace->canonical_url ?: $url_destino }}">
    
    <!-- Cache Control -->
    <!-- Header: Cache-Control: public, max-age=86400 (24 horas) -->
</head>
<body>
    @if(isset($preview_mode) && $preview_mode)
        <!-- Banner solo visible en modo ?preview=1 -->
        <div class="preview-banner">
            <h2>🔍 Modo Preview - Testing SEO</h2>
            <p>Vista previa para bots de redes sociales</p>
            <p>URL destino: <a href="{{ $url_destino }}">{{ $url_destino }}</a></p>
        </div>
    @endif
    
    <!-- Contenido para bots que renderizan -->
    <div class="content">
        <h1>{{ $enlace->titulo ?: 'Contenido compartido' }}</h1>
        @if($enlace->descripcion)
            <p>{{ $enlace->descripcion }}</p>
        @endif
        @if($enlace->og_imagen)
            <img src="{{ $enlace->og_imagen }}" alt="{{ $enlace->titulo }}">
        @endif
        <a href="{{ $url_destino }}">Ver contenido completo</a>
    </div>
</body>
</html>
```

### Campos SEO en Base de Datos

```sql
-- Metadatos básicos
meta_titulo VARCHAR(60)
meta_descripcion VARCHAR(160)
meta_keywords VARCHAR(255)
canonical_url VARCHAR(255)

-- Open Graph (Facebook, LinkedIn)
og_titulo VARCHAR(95)
og_descripcion TEXT
og_imagen VARCHAR(255)
og_tipo VARCHAR(20) DEFAULT 'website'

-- Twitter Cards
twitter_card VARCHAR(20) DEFAULT 'summary'
twitter_titulo VARCHAR(70)
twitter_descripcion TEXT
twitter_imagen VARCHAR(255)
```

## 🎨 Frontend (Vue.js)

### Composable useEnlacesCortos

```javascript
import { useEnlacesCortos } from '@/composables/useEnlacesCortos'

const {
    obtenerEnlaceCorto, // Función principal - la única que necesitas
    obtenerInfo,
    obtenerEstadisticas
} = useEnlacesCortos()
```

### Ejemplos de Uso

#### Uso Universal - Una Sola Función
```javascript
// Para cualquier URL - detección automática de prefijo
const urlCorta = await obtenerEnlaceCorto('https://example.com/very-long-url')
console.log(urlCorta) // https://tseyor.org/e/xyz789 (o URL original si es corta)

// Para documentos - detecta automáticamente el prefijo 'd'
const urlCorta = await obtenerEnlaceCorto('https://example.com/documento.pdf')
console.log(urlCorta) // https://tseyor.org/d/abc123

// Para eventos, noticias, etc. - usa prefijo 'e' automáticamente
const urlCorta = await obtenerEnlaceCorto('https://tseyor.org/eventos/encuentro-2024')
console.log(urlCorta) // https://tseyor.org/e/encuentro24

// URLs cortas no se modifican
const urlCorta = await obtenerEnlaceCorto('https://tseyor.org/inicio')
console.log(urlCorta) // https://tseyor.org/inicio (sin cambios)
```

#### Detección Automática de Documentos
```javascript
// Estas extensiones se detectan automáticamente como documentos (prefijo 'd'):
// .pdf, .doc, .docx, .ppt, .pptx, .xls, .xlsx

await obtenerEnlaceCorto('https://example.com/manual.pdf')     // → /d/abc123
await obtenerEnlaceCorto('https://example.com/presentacion.pptx') // → /d/def456
await obtenerEnlaceCorto('https://example.com/hoja.xlsx')      // → /d/ghi789

// Todo lo demás usa prefijo 'e' (enlaces generales)
await obtenerEnlaceCorto('https://example.com/evento-especial') // → /e/xyz123
await obtenerEnlaceCorto('https://example.com/noticia-importante') // → /e/abc456
```

## 🔍 SEO y Metadatos

### Campos SEO Disponibles

El sistema extrae y almacena metadatos específicos:

#### Meta Tags Básicos
- `meta_titulo` (máx. 60 caracteres)
- `meta_descripcion` (máx. 160 caracteres)
- `meta_keywords`
- `canonical_url`

#### Open Graph (Facebook)
- `og_titulo` (máx. 95 caracteres)
- `og_descripcion`
## 🎨 Frontend (Vue.js)

### Composable useEnlacesCortos

```javascript
// resources/js/composables/useEnlacesCortos.js
import { useEnlacesCortos } from '@/composables/useEnlacesCortos'

const {
    acortarParaCompartir, // 🎯 Función principal - unificada y simplificada
    obtenerEnlace,        // Función base (llamada interna)
    cargando,             // Estado reactivo de carga
    error                 // Error reactivo si hay problemas
} = useEnlacesCortos()

// Uso típico en Share.vue
const prepararEnlaceCorto = async () => {
    const urlCorta = await acortarParaCompartir(fullUrl.value)
    if (urlCorta !== fullUrl.value) {
        currentUrl.value = urlCorta // URL acortada exitosamente
    }
    // Si no se acortó, currentUrl mantiene la URL original
}
```

**Características del composable**:
- Una sola función para todo: `acortarParaCompartir()`
- Manejo automático de errores
- Estados reactivos (`cargando`, `error`)
- Llamada unificada a `/obtener-enlace`

### Integración en Share.vue

```vue
<script setup>
const { acortarParaCompartir } = useEnlacesCortos()

const props = defineProps({
    url: String,
    titulo: String,
    descripcion: String
})

const currentUrl = ref(props.url)
const socialShow = ref(false)

const toggleSocialShow = () => {
    socialShow.value = true
    // Crear enlace corto en segundo plano mientras usuario elige plataforma
    prepararEnlaceCorto()
}

const prepararEnlaceCorto = async () => {
    // Una sola llamada que maneja todo:
    // 1. Verifica si ya existe
    // 2. Evalúa si necesita acortarse (umbral)
    // 3. Crea el enlace si es necesario
    // 4. Devuelve URL corta o URL original
    const urlCorta = await acortarParaCompartir(currentUrl.value)
    
    if (urlCorta !== currentUrl.value) {
        console.log('URL acortada:', urlCorta)
        currentUrl.value = urlCorta // Actualizar URL para compartir
    } else {
        console.log('URL original mantenida (no supera umbral)')
    }
}

// Compartir en red social específica
const compartirEn = (red) => {
    const urls = {
        facebook: `https://facebook.com/sharer.php?u=${encodeURIComponent(currentUrl.value)}`,
        twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl.value)}&text=${encodeURIComponent(props.titulo)}`,
        whatsapp: `https://wa.me/?text=${encodeURIComponent(props.titulo + ' ' + currentUrl.value)}`,
        telegram: `https://t.me/share/url?url=${encodeURIComponent(currentUrl.value)}&text=${encodeURIComponent(props.titulo)}`
    }
    
    window.open(urls[red], '_blank', 'width=600,height=400')
}
</script>

<template>
    <div class="share-component">
        <button @click="toggleSocialShow">
            <i class="fa fa-share"></i> Compartir
        </button>
        
        <transition name="fade">
            <div v-if="socialShow" class="social-options">
                <button @click="compartirEn('facebook')">
                    <i class="fab fa-facebook"></i> Facebook
                </button>
                <button @click="compartirEn('twitter')">
                    <i class="fab fa-twitter"></i> Twitter
                </button>
                <button @click="compartirEn('whatsapp')">
                    <i class="fab fa-whatsapp"></i> WhatsApp
                </button>
                <button @click="compartirEn('telegram')">
                    <i class="fab fa-telegram"></i> Telegram
                </button>
                
                <!-- Mostrar URL corta si fue generada -->
                <div v-if="currentUrl !== props.url" class="short-url">
                    <span>Enlace corto:</span>
                    <code>{{ currentUrl }}</code>
                    <button @click="copiarAlPortapapeles(currentUrl)">
                        <i class="fa fa-copy"></i>
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>
```

### Ventajas del Enfoque Bajo Demanda

1. **No se crean enlaces innecesarios**: Solo cuando el usuario comparte
2. **Transparente**: Creación en segundo plano mientras usuario decide
3. **Una sola llamada**: Backend maneja toda la lógica
4. **Eficiente**: Reutiliza enlaces existentes automáticamente

## 📊 Estadísticas y Tracking

### Google Analytics Measurement Protocol

Cada evento relacionado con enlaces cortos se trackea automáticamente usando Google Analytics 4 Measurement Protocol:

#### Eventos Trackeados

1. **`enlace_corto_click`**: Cada vez que un usuario hace clic en un enlace corto
   - Parámetros: `prefijo`, `codigo`, `url_destino`, `user_agent`, `referrer`

2. **`enlace_corto_creado`**: Cuando se crea un nuevo enlace corto
   - Parámetros: `prefijo`, `codigo`, `url_original`, `fue_nuevo: true`

3. **`enlace_corto_reutilizado`**: Cuando se reutiliza un enlace existente
   - Parámetros: `prefijo`, `codigo`, `url_original`, `fue_nuevo: false`

#### Configuración Requerida

En tu archivo `.env`:
```env
# Google Analytics 4
GA_MEASUREMENT_ID=G-XXXXXXXXXX
GA_API_SECRET=your_measurement_protocol_secret

# Enlaces cortos
ENLACES_CORTOS_ESTADISTICAS=true
ENLACES_CORTOS_TRACK_USER_AGENT=true
ENLACES_CORTOS_TRACK_REFERER=true
```

#### Tracking Automático en Controller

```php
// EnlaceCortoController::redirigir()
public function redirigir(string $prefijo, string $codigo)
{
    $enlace = EnlaceCorto::where('prefijo', $prefijo)
        ->where('codigo', $codigo)
        ->where('activo', true)
        ->first();
    
    if (!$enlace) {
        abort(404, 'Enlace no encontrado');
    }
    
    // 1. Actualizar contador en BD
    $this->enlaceService->actualizarContadorClics($prefijo, $codigo);
    
    // 2. Trackear en Google Analytics
    $analyticsController = app(AnalyticsController::class);
    $analyticsController->trackEnlaceCorto($prefijo, $codigo, $enlace->url_original, request());
    
    // 3. Redireccionar o mostrar preview
    if ($this->esRedSocialCompartiendo() || request()->query('preview') == '1') {
        return response()->view('enlaces-cortos.preview', [/* ... */]);
    }
    
    return redirect($enlace->url_original, 301);
}
```

#### Tracking en Creación/Reutilización

```php
// EnlaceCortoService::obtenerEnlaceParaUrl()
public function obtenerEnlaceParaUrl(string $url, &$fueAcortada = null, &$existia = null): ?EnlaceCorto
{
    // Buscar enlace existente
    $enlace = EnlaceCorto::where('url_original', $url)
        ->where('activo', true)
        ->first();
    
    if ($enlace) {
        // Trackear reutilización
        try {
            AnalyticsHelper::trackCreacionEnlace($enlace->prefijo, $enlace->codigo, $url, false);
        } catch (\Exception $e) {
            Log::warning('Error tracking enlace reuse', ['error' => $e->getMessage()]);
        }
        
        $fueAcortada = true;
        $existia = true;
        return $enlace;
    }
    
    // Crear nuevo enlace
    try {
        $enlace = $this->crear($url);
        
        // Trackear creación (se hace automáticamente en crear())
        AnalyticsHelper::trackCreacionEnlace($enlace->prefijo, $enlace->codigo, $url, true);
        
        $fueAcortada = true;
        $existia = false;
        return $enlace;
    } catch (\Exception $e) {
        Log::error('Error creando enlace corto', ['url' => $url, 'error' => $e->getMessage()]);
        return null;
    }
}
```

### Métricas en Base de Datos

```php
// app/Models/EnlaceCorto.php
class EnlaceCorto extends Model
{
    protected $fillable = [
        'codigo',
        'prefijo',
        'url_original',
        'clics',           // Contador de clics totales
        'ultimo_clic',     // Timestamp del último clic
        'activo',
        // ... más campos
    ];
    
    public function incrementarClics()
    {
        $this->increment('clics');
        $this->update(['ultimo_clic' => now()]);
    }
}
```

### AnalyticsHelper (Utility Class)

```php
// app/Pigmalion/AnalyticsHelper.php
use App\Pigmalion\AnalyticsHelper;

// Trackear creación/reutilización
AnalyticsHelper::trackCreacionEnlace(
    prefijo: 'e',
    codigo: 'abc123',
    urlOriginal: 'https://example.com/long-url',
    fueNuevo: true  // true = creado, false = reutilizado
);

// Trackear evento personalizado
AnalyticsHelper::trackEventoPersonalizado('enlace_compartido', [
    'plataforma' => 'facebook',
    'tipo_contenido' => 'evento',
    'prefijo' => 'e',
    'codigo' => 'abc123'
]);
```

### Queries Útiles para Estadísticas

```php
// Enlaces más populares
$populares = EnlaceCorto::where('activo', true)
    ->orderBy('clics', 'desc')
    ->limit(10)
    ->get();

// Enlaces recientes
$recientes = EnlaceCorto::where('activo', true)
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

// Total de clics
$totalClics = EnlaceCorto::where('activo', true)->sum('clics');

// Enlaces por prefijo
$estadisticas = EnlaceCorto::selectRaw('prefijo, COUNT(*) as total, SUM(clics) as total_clics')
    ->where('activo', true)
    ->groupBy('prefijo')
    ->get();
```

## 🔧 Arquitectura Técnica

### Separación de Responsabilidades (DRY)

#### EnlaceCortoController (Ultra-Simplificado)

```php
class EnlaceCortoController extends Controller
{
    public function redirigir($prefijo, $codigo, CrawlerDetect $crawlerDetect)
    {
        // 1. Buscar enlace
        $enlace = $this->enlaceCortoService->obtenerEnlaceParaUrl($prefijo, $codigo, $url, $encontrado);
        
        // 2. Si es bot social -> HTML con metadatos
        if ($crawlerDetect->isCrawler()) {
            return view('enlaces-cortos.preview', compact('enlace'));
        }
        
        // 3. Si es usuario -> 301 redirect
        $this->enlaceCortoService->actualizarContadorClics($enlace->id);
        return redirect($url, 301);
    }
    
    public function obtener(Request $request)
    {
        // Delegación total al servicio
        return $this->enlaceCortoService->crearOBuscarEnlaceCorto($request->url);
    }
}
```

#### EnlaceCortoService (Centro de Lógica)

```php
class EnlaceCortoService
{
    public function actualizarContadorClics($enlaceId)
    {
        // Actualización optimizada del contador
        EnlaceCorto::where('id', $enlaceId)->increment('contador_clics');
    }
    
    public function obtenerEnlaceParaUrl($prefijo, $codigo, &$url, &$encontrado)
    {
        // Parámetros por referencia para optimizar returns
        $enlace = EnlaceCorto::where([
            'prefijo' => $prefijo,
            'codigo' => $codigo,
            'activo' => true
        ])->first();
        
        $url = $enlace?->url_original;
        $encontrado = $enlace !== null;
        
        return $enlace;
    }
    
    public function crearOBuscarEnlaceCorto($url)
    {
        // Lógica centralizada de creación/búsqueda
        // Manejo de umbrales, códigos únicos, metadatos SEO
    }
}
```

#### ContenidoHelper (Integración Automática)

```php
class ContenidoHelper
{
    public static function onContenidoSaved($contenido)
    {
        // Crear/actualizar enlace corto automáticamente
        // Extraer metadatos SEO del contenido
        self::extraerSeoDelContenido($contenido);
    }
    
    public static function onContenidoDeleted($contenido)
    {
        // Desactivar enlaces relacionados (no eliminar por SEO)
        self::desactivarEnlacesRelacionados($contenido);
    }
    
    private static function extraerSeoDelContenido($contenido)
    {
        // Extracción de metadatos desde contenido HTML
        // Sincronización con tabla enlaces_cortos
    }
}
```

### Flujo de Eventos sin Observers

```php
// En app/Models/Contenido.php
protected static function boot()
{
    parent::boot();
    
    static::saved(function ($contenido) {
        ContenidoHelper::onContenidoSaved($contenido);
    });
    
    static::deleted(function ($contenido) {
        ContenidoHelper::onContenidoDeleted($contenido);
    });
}
```

### Detección Profesional de Bots

```php
// jaybizzle/crawler-detect library
use Jaybizzle\CrawlerDetect\CrawlerDetect;

$crawlerDetect = new CrawlerDetect;

// Detecta automáticamente: Facebook, WhatsApp, Twitter, LinkedIn, etc.
if ($crawlerDetect->isCrawler()) {
    // Bot social -> Mostrar HTML con metadatos
    return view('enlaces-cortos.preview', compact('enlace'));
} else {
    // Usuario real -> 301 redirect
    return redirect($enlace->url_original, 301);
}
```

## 🧪 Testing y Validación

### Comandos de Prueba con curl (PowerShell/Windows)

#### 1. Simular Bot de Facebook
```powershell
curl.exe -H "User-Agent: facebookexternalhit/1.1" https://tseyor.org/e/codigo123
```

**Respuesta esperada**:
- Status: `200 OK`
- Content-Type: `text/html`
- Body: HTML completo con metadatos Open Graph
- Cache-Control: `public, max-age=86400`

#### 2. Simular Bot de WhatsApp
```powershell
curl.exe -H "User-Agent: WhatsApp/2.23.2.72" https://tseyor.org/e/codigo123
```

**Respuesta esperada**:
- Status: `200 OK`
- Body: HTML con metadatos para preview de WhatsApp

#### 3. Simular Usuario Normal
```powershell
curl.exe -I -H "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)" https://tseyor.org/e/codigo123
```

**Respuesta esperada**:
- Status: `301 Moved Permanently`
- Location: `https://tseyor.org/destino/real`
- Sin body (redirección directa)

#### 4. Modo Preview (Testing SEO)
```powershell
curl.exe https://tseyor.org/e/codigo123?preview=1
```

**Respuesta esperada**:
- Status: `200 OK`
- Body: HTML con banner "🔍 Modo Preview - Testing SEO"
- Muestra metadatos completos como los bots sociales

#### 5. Código Inexistente
```powershell
curl.exe -I https://tseyor.org/e/noexiste
```

**Respuesta esperada**:
- Status: `404 Not Found`
- Body: Página de error "Enlace no encontrado o expirado"

### Testing con Herramientas SEO Online

#### Facebook Sharing Debugger
```
https://developers.facebook.com/tools/debug/

Probar URL: https://tseyor.org/e/codigo123
```

**Verificar**:
- Título (og:title)
- Descripción (og:description)
- Imagen (og:image)
- Tipo (og:type)

#### Twitter Card Validator
```
https://cards-dev.twitter.com/validator

Probar URL: https://tseyor.org/e/codigo123
```

**Verificar**:
- Twitter Card type (summary/summary_large_image)
- Twitter título
- Twitter descripción
- Twitter imagen

#### LinkedIn Post Inspector
```
https://www.linkedin.com/post-inspector/

Probar URL: https://tseyor.org/e/codigo123
```

**Verificar**:
- Open Graph metadatos
- Imagen preview
- Descripción correcta

### Script de Testing Automatizado

```powershell
# test-enlaces-cortos.ps1

$BASE_URL = "https://tseyor.org"
$CODIGO = "codigo123"
$URL_TEST = "$BASE_URL/e/$CODIGO"

Write-Host "=== Testing Enlaces Cortos ===" -ForegroundColor Cyan

# Test 1: Bot de Facebook
Write-Host "`n1. Bot de Facebook..." -ForegroundColor Yellow
$response = curl.exe -s -w "\nStatus: %{http_code}\n" -H "User-Agent: facebookexternalhit/1.1" $URL_TEST
if ($response -match "Status: 200") {
    Write-Host "✓ Facebook bot OK" -ForegroundColor Green
} else {
    Write-Host "✗ Facebook bot FAIL" -ForegroundColor Red
}

# Test 2: Usuario normal
Write-Host "`n2. Usuario normal..." -ForegroundColor Yellow
$response = curl.exe -s -I -w "\nStatus: %{http_code}\n" -H "User-Agent: Mozilla/5.0" $URL_TEST
if ($response -match "Status: 301") {
    Write-Host "✓ Redirección 301 OK" -ForegroundColor Green
} else {
    Write-Host "✗ Redirección FAIL" -ForegroundColor Red
}

# Test 3: Modo preview
Write-Host "`n3. Modo preview..." -ForegroundColor Yellow
$response = curl.exe -s -w "\nStatus: %{http_code}\n" "$URL_TEST?preview=1"
if ($response -match "Modo Preview") {
    Write-Host "✓ Preview mode OK" -ForegroundColor Green
} else {
    Write-Host "✗ Preview mode FAIL" -ForegroundColor Red
}

# Test 4: Código inexistente
Write-Host "`n4. Código inexistente..." -ForegroundColor Yellow
$response = curl.exe -s -I -w "\nStatus: %{http_code}\n" "$BASE_URL/e/noexiste"
if ($response -match "Status: 404") {
    Write-Host "✓ Error 404 OK" -ForegroundColor Green
} else {
    Write-Host "✗ Error 404 FAIL" -ForegroundColor Red
}

Write-Host "`n=== Tests completados ===" -ForegroundColor Cyan
```

### Verificación de Logs

```php
// storage/logs/laravel.log

// Log cuando se muestra preview
[2025-10-03 10:15:30] local.INFO: Mostrando preview de enlace corto {
    "url_corta": "https://tseyor.org/e/codigo123",
    "url_destino": "https://tseyor.org/eventos/encuentro-2024",
    "user_agent": "facebookexternalhit/1.1",
    "prefijo": "e",
    "codigo": "codigo123",
    "tiene_titulo": true,
    "tiene_og_imagen": true,
    "es_bot_social": true,
    "preview_forzado": false
}

// Log cuando se crea enlace
[2025-10-03 10:16:45] local.INFO: Enlace corto creado {
    "codigo": "codigo123",
    "prefijo": "e",
    "tipo": "contenido_interno",
    "tiene_contenido_relacionado": true,
    "url_original": "https://tseyor.org/eventos/encuentro-2024"
}

// Log de decisión de acortamiento
[2025-10-03 10:17:20] local.INFO: [EnlaceCortoService] Decisión final basada en longitud total {
    "necesita_acortar": true,
    "longitud_url": 85
}
```

### Pruebas Unitarias

```php
// tests/Feature/EnlaceCortoTest.php

public function test_bot_social_recibe_html_con_metadatos()
{
    $enlace = EnlaceCorto::factory()->create();
    
    $response = $this->withHeaders([
        'User-Agent' => 'facebookexternalhit/1.1'
    ])->get("/{$enlace->prefijo}/{$enlace->codigo}");
    
    $response->assertStatus(200);
    $response->assertViewIs('enlaces-cortos.preview');
    $response->assertSee($enlace->titulo);
    $response->assertSee('og:title');
}

public function test_usuario_normal_recibe_redireccion_301()
{
    $enlace = EnlaceCorto::factory()->create();
    
    $response = $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0'
    ])->get("/{$enlace->prefijo}/{$enlace->codigo}");
    
    $response->assertStatus(301);
    $response->assertRedirect($enlace->url_original);
}

public function test_modo_preview_muestra_banner()
{
    $enlace = EnlaceCorto::factory()->create();
    
    $response = $this->get("/{$enlace->prefijo}/{$enlace->codigo}?preview=1");
    
    $response->assertStatus(200);
    $response->assertSee('Modo Preview');
    $response->assertSee('Testing SEO');
}

public function test_url_corta_no_se_acorta()
{
    $urlCorta = 'https://tseyor.org/inicio';
    
    $response = $this->postJson('/obtener-enlace', ['url' => $urlCorta]);
    
    $response->assertJson([
        'url_corta' => $urlCorta,
        'fue_acortada' => false
    ]);
}

public function test_url_larga_se_acorta()
{
    $urlLarga = 'https://tseyor.org/eventos/encuentro-anual-interdimensional-2024-muy-largo';
    
    $response = $this->postJson('/obtener-enlace', ['url' => $urlLarga]);
    
    $response->assertJson(['fue_acortada' => true]);
    $response->assertJsonStructure(['url_corta']);
}
```

## ⚡ Administración

### Comandos Artisan

```bash
# Ejecutar migración
php artisan migrate

# Generar códigos únicos para enlaces existentes
php artisan enlaces:regenerar-codigos

# Limpiar enlaces inactivos 
php artisan enlaces:limpiar --dias=365

# Estadísticas generales
php artisan enlaces:stats
```

### Backpack Admin Panel

Gestión completa desde el panel de administración:
- Lista de todos los enlaces cortos
- Estadísticas por enlace
- Activar/desactivar enlaces
- Editar metadatos SEO
- Ver historial de clics

## 📝 Ejemplos Completos

### Caso 1: Crear Enlace desde Frontend

```javascript
// En cualquier componente Vue
import { useEnlacesCortos } from '@/composables/useEnlacesCortos'

const { acortarParaCompartir } = useEnlacesCortos()

// Función que se ejecuta al compartir
const compartirEnRedes = async () => {
    const urlCorta = await acortarParaCompartir(window.location.href)
    
    // Si se acortó, usar la URL corta
    const urlParaCompartir = urlCorta
    
    // Abrir ventana de compartir con URL corta
    window.open(`https://facebook.com/sharer.php?u=${encodeURIComponent(urlParaCompartir)}`)
}
```

### Caso 2: Procesamiento Automático en Contenido

```php
// Cuando se guarda contenido, se crea automáticamente enlace corto
// No requiere código adicional - se maneja automáticamente via ContenidoHelper

$noticia = new Contenido();
$noticia->titulo = 'Nuevo Encuentro Interdimensional 2024';
$noticia->contenido = '<p>Gran evento espiritual...</p>';
$noticia->url_amigable = 'encuentro-interdimensional-2024';
$noticia->save(); 

// ✅ Automáticamente se crea:
// - Enlace: https://tseyor.org/e/enc2024
// - SEO: Extracción automática de metadatos
// - Sincronización: Base de datos actualizada
```

### Caso 3: Gestión Manual de Metadatos

```php
// Si necesitas control total sobre metadatos SEO
use App\Services\EnlaceCortoService;

$service = app(EnlaceCortoService::class);

$enlace = $service->crearOBuscarEnlaceCorto('https://example.com/evento-especial', [
    'meta_titulo' => 'Evento Especial - TSEYOR',
    'meta_descripcion' => 'Únete a nuestro evento interdimensional',
    'og_imagen' => 'https://tseyor.org/img/evento-especial.jpg',
    'prefijo_personalizado' => 'evento'
]);
```

---

## 🔄 Historial de Cambios

### v2.0.0 (Actual) - Refactoring DRY y Bot Detection
- ✅ **DRY**: Código centralizado en EnlaceCortoService
- ✅ **Bot Detection**: Librería profesional jaybizzle/crawler-detect
- ✅ **Observer → Model Events**: Eliminado ContenidoObserver, integrado en model boot()
- ✅ **Controller Simplificado**: 86 líneas, lógica delegada al servicio
- ✅ **SEO Preview**: Vista HTML específica para bots sociales
- ✅ **Testing**: Comandos curl para validación en Windows

### v1.0.0 - Sistema Inicial
- Funcionalidad básica de enlaces cortos
- Integración con Google Analytics
- Prefijos automáticos por tipo de contenido

### Base de Datos

#### Tabla `enlaces_cortos`
```sql
CREATE TABLE enlaces_cortos (
    id BIGINT PRIMARY KEY,
    codigo VARCHAR(20) UNIQUE,
    url_original TEXT,
    prefijo VARCHAR(10) DEFAULT 's',
    titulo VARCHAR(255),
    descripcion TEXT,
    user_id BIGINT NULL,

    -- SEO Fields
    meta_titulo VARCHAR(60),
    meta_descripcion VARCHAR(160),
    meta_keywords VARCHAR(255),
    og_titulo VARCHAR(95),
    og_descripcion TEXT,
    og_imagen VARCHAR(255),
    og_tipo VARCHAR(20) DEFAULT 'website',
    twitter_card VARCHAR(20) DEFAULT 'summary',
    twitter_titulo VARCHAR(70),
    twitter_descripcion TEXT,
    twitter_imagen VARCHAR(255),
    canonical_url VARCHAR(255),

    -- Stats
    clics BIGINT DEFAULT 0,
    ultimo_clic TIMESTAMP NULL,
    activo BOOLEAN DEFAULT TRUE,

    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 📚 Ejemplos

### Ejemplo 1: Evento con SEO Completo

```php
$evento = \App\Models\Evento::find(1);
$enlace = app(EnlaceCortoService::class)->crear(
    'https://tseyor.org/eventos/encuentro-anual-2024',
    'encuentro2024', // código personalizado
    'e', // prefijo de evento
    'Encuentro Anual TSEYOR 2024',
    'Gran encuentro presencial de hermanos TSEYOR en Madrid',
    auth()->user(),
    [
        'meta_titulo' => 'Encuentro TSEYOR 2024 | Evento Espiritual Madrid',
        'meta_descripcion' => 'Únete al gran encuentro anual de TSEYOR 2024 en Madrid. Conferencias, meditaciones y hermandad espiritual.',
        'meta_keywords' => 'TSEYOR, encuentro, 2024, Madrid, espiritualidad',
        'og_titulo' => 'Encuentro Anual TSEYOR 2024',
        'og_descripcion' => 'Tres días de conferencias, talleres y meditaciones en el mayor evento de TSEYOR del año',
        'og_imagen' => 'https://tseyor.org/storage/eventos/encuentro2024-og.jpg',
        'og_tipo' => 'event',
        'twitter_card' => 'summary_large_image',
        'twitter_titulo' => 'Encuentro TSEYOR 2024 Madrid',
        'twitter_descripcion' => 'El evento espiritual del año. ¡Reserva tu plaza!',
        'twitter_imagen' => 'https://tseyor.org/storage/eventos/encuentro2024-twitter.jpg'
    ]
);

echo $enlace->url_corta; // https://tseyor.org/e/encuentro2024
```

### Ejemplo 2: Uso en Componente Vue

```vue
<template>
    <div class="evento-card">
        <h3>{{ evento.titulo }}</h3>
        <p>{{ evento.descripcion }}</p>

        <div class="acciones">
            <button @click="compartir">Compartir</button>
            <button @click="copiarEnlace">Copiar Enlace Corto</button>
        </div>

        <div v-if="enlaceCorto" class="enlace-info">
            <p>Enlace corto: <code>{{ enlaceCorto }}</code></p>
            <p>Clics: {{ estadisticas.clics }}</p>
        </div>
    </div>
</template>

<script setup>
const props = defineProps(['evento'])
const { crearParaEvento, obtenerInfo } = useEnlacesCortos()

const enlaceCorto = ref(null)
const estadisticas = ref({})

onMounted(async () => {
    // Crear enlace corto para el evento
    const resultado = await crearParaEvento({
        url: `https://tseyor.org/eventos/${props.evento.slug}`,
        titulo: props.evento.titulo,
        descripcion: props.evento.descripcion,
        imagen: props.evento.imagen
    })

    enlaceCorto.value = resultado.data.url_corta

    // Obtener estadísticas
    const info = await obtenerInfo('e', resultado.data.codigo)
    estadisticas.value = info
})

const compartir = () => {
    if (navigator.share) {
        navigator.share({
            title: props.evento.titulo,
            text: props.evento.descripcion,
            url: enlaceCorto.value
        })
    }
}

const copiarEnlace = async () => {
    await navigator.clipboard.writeText(enlaceCorto.value)
    alert('Enlace copiado al portapapeles')
}
</script>
```

### Ejemplo 3: Integración en Backpack

```php
// En un CrudController de Backpack
public function setup()
{
    // ... configuración normal ...

    $this->crud->addColumn([
        'name' => 'enlace_corto',
        'label' => 'Enlace Corto',
        'type' => 'custom_html',
        'value' => function($entry) {
            $enlace = EnlacesHelper::crearParaContenido($entry, 'e');
            return $enlace ?
                '<a href="'.$enlace.'" target="_blank" class="btn btn-sm btn-primary">
                    <i class="la la-link"></i> Ver Enlace Corto
                </a>' :
                '<small class="text-muted">No disponible</small>';
        }
    ]);
}
```

## 🔧 Solución de Problemas

### Problemas Comunes

#### 1. Error 404 en enlaces cortos
- Verificar que las rutas estén registradas correctamente
- Comprobar que el enlace existe en la base de datos
- Revisar la configuración de prefijos

#### 2. No se crean enlaces automáticamente
- Verificar `ENLACES_CORTOS_HABILITADO=true` en `.env`
- Comprobar que los model events están funcionando
- Revisar logs en `storage/logs/laravel.log`

#### 3. Metadatos SEO no se extraen
- Verificar conectividad a la URL externa
- Comprobar que la URL devuelve HTML válido
- Revisar timeout en `extraerMetadatosSEO`

### Logs y Debugging

```php
// Habilitar logs detallados
Log::info('Enlaces cortos debug', [
    'config' => config('enlaces_cortos'),
    'modelo' => get_class($modelo),
    'url' => $url
]);
```

### Performance

- Los enlaces se cachean por 60 minutos por defecto
- Las estadísticas se actualizan de forma asíncrona
- Los metadatos SEO se extraen solo una vez al crear

---

## 📞 Soporte

Para problemas o sugerencias relacionadas con el sistema de enlaces cortos:

1. Revisar logs en `storage/logs/laravel.log`
2. Verificar configuración en `config/enlaces_cortos.php`
3. Comprobar variables de entorno `.env`
4. Consultar esta documentación

## 🔄 Historial de Cambios y Evolución

### v2.1.0 (Octubre 2025) - Detección Específica de Bots
- ✅ **Lista personalizada de bots**: Eliminada dependencia de `jaybizzle/crawler-detect`
- ✅ **Preview forzado**: Parámetro `?preview=1` para testing SEO
- ✅ **Handler.php**: Método `esBotRedSocial()` reutilizable en toda la app
- ✅ **Logging detallado**: Información completa de detección de bots y metadatos
- ✅ **Cache de 24h**: Header `Cache-Control` en vista preview
- ✅ **Banner informativo**: En modo preview para distinguir de producción

### v2.0.0 (Septiembre 2025) - Creación Bajo Demanda
- ✅ **Creación bajo demanda**: Enlaces generados solo al compartir (Share.vue)
- ✅ **Umbral inteligente**: Múltiples criterios para decidir si acortar
- ✅ **DRY Architecture**: Lógica centralizada en EnlaceCortoService
- ✅ **Vista preview.blade.php**: HTML específico para bots sociales
- ✅ **Metadatos automáticos**: Extracción desde Contenido, archivos o scraping
- ✅ **Endpoint unificado**: `/obtener-enlace` maneja toda la lógica
- ✅ **Google Analytics**: Tracking automático de eventos

### v1.0.0 (Agosto 2025) - Sistema Inicial
- Funcionalidad básica de enlaces cortos
- Prefijos automáticos por tipo de contenido (e/d/a)
- Integración con modelo Contenido
- Panel de administración en Backpack

## 📝 Notas Técnicas Importantes

### ¿Por Qué No Usar Librerías Genéricas de Detección?

**Problema**: Librerías como `jaybizzle/crawler-detect` detectan TODOS los crawlers, incluyendo:
- Motores de búsqueda (Google, Bing)
- Herramientas SEO (Semrush, Ahrefs, Seobility)
- Monitores de uptime
- Scrapers genéricos

**Consecuencia**: Estas herramientas verían el HTML de preview en lugar de la redirección real, lo que distorsiona su análisis.

**Solución**: Lista específica mantenida manualmente solo con bots de redes sociales y mensajería.

### Arquitectura de Servicios

```
Request → EnlaceCortoController
    ↓
    ├─ esRedSocialCompartiendo() [Lista específica]
    ↓
    ├─ EnlaceCortoService::obtenerEnlaceParaUrl()
    │   ├─ buscarContenidoRelacionado()
    │   ├─ necesitaAcortarse() [Umbral inteligente]
    │   ├─ extraerMetadatosDesdeContenido()
    │   └─ crear() [Con metadatos SEO completos]
    ↓
    └─ Response
        ├─ Bot social → view('enlaces-cortos.preview')
        └─ Usuario → redirect(301)
```

### Campos de Base de Datos

La tabla `enlaces_cortos` almacena metadatos completos para evitar scraping repetido:

```sql
-- Metadatos básicos
titulo VARCHAR(255)
descripcion TEXT
contenido_id BIGINT NULL  -- Relación con tabla contenidos

-- SEO estándar
meta_titulo VARCHAR(60)
meta_descripcion VARCHAR(160)
meta_keywords VARCHAR(255)
canonical_url VARCHAR(255)

-- Open Graph
og_titulo VARCHAR(95)
og_descripcion TEXT
og_imagen VARCHAR(255)
og_tipo VARCHAR(20) DEFAULT 'website'

-- Twitter Cards
twitter_card VARCHAR(20) DEFAULT 'summary'
twitter_titulo VARCHAR(70)
twitter_descripcion TEXT
twitter_imagen VARCHAR(255)

-- Estadísticas
clics BIGINT DEFAULT 0
ultimo_clic TIMESTAMP NULL
activo BOOLEAN DEFAULT TRUE

-- Auditoría
created_at TIMESTAMP
updated_at TIMESTAMP
```

### Configuración Recomendada

```env
# Enlaces cortos habilitados
ENLACES_CORTOS_HABILITADO=true

# Dominio principal
ENLACES_CORTOS_DOMINIO=https://tseyor.org

# Umbrales de acortamiento
ENLACES_CORTOS_UMBRAL_PATH=30
ENLACES_CORTOS_URL_MAXIMA_CORTA=60

# Código único
ENLACES_CORTOS_LONGITUD_CODIGO=6
ENLACES_CORTOS_EXCLUIR_SIMILARES=true

# Estadísticas
ENLACES_CORTOS_ESTADISTICAS=true
GA_MEASUREMENT_ID=G-XXXXXXXXXX
GA_API_SECRET=your_measurement_protocol_secret

# Cache
ENLACES_CORTOS_CACHE_TTL=60
```

### Patrones Excluidos (Opcional)

En `config/enlaces_cortos.php`:

```php
'patrones_excluidos' => [
    '^/inicio$',           // Página de inicio
    '^/contacto$',         // Página de contacto
    '^/admin',             // Panel de administración
    '^/api',               // Endpoints de API
],
```

### Consideraciones de Performance

1. **Cache de 24 horas** en vista preview (bots sociales)
2. **Actualización asíncrona** de contadores de clics
3. **Reutilización** de enlaces existentes (evita duplicados)
4. **Scraping con timeout** de 5 segundos para URLs externas
5. **Logging selectivo** solo en development/staging

### Seguridad

- Rate limiting en rutas de creación
- Validación de URLs (FILTER_VALIDATE_URL)
- Protección contra URLs maliciosas en configuración
- Enlaces desactivados (no eliminados) para preservar SEO

---

**Documentación actualizada**: 3 de octubre de 2025

**Mantenedores**: Equipo de Desarrollo TSEYOR.org

**Versión del sistema**: v2.1.0
