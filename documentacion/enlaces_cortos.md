# Sistema de Enlaces Cortos - TSEYOR.org

El sistema de enlaces cortos de TSEYOR permite crear URLs cortas y amigables para compartir contenido, con funcionalidades avanzadas de SEO y estadísticas integradas con Google Analytics.

## 📋 Índice

- [Características Principales](#características-principales)
- [Configuración](#configuración)
- [Estructura de URLs](#estructura-de-urls)
- [Uso Automático](#uso-automático)
- [API](#api)
- [Frontend (Vue.js)](#frontend-vuejs)
- [SEO y Metadatos](#seo-y-metadatos)
- [Estadísticas](#estadísticas)
- [Administración](#administración)
- [Ejemplos](#ejemplos)

## 🚀 Características Principales

- **Enlaces permanentes**: Los enlaces cortos nunca expiran
- **SEO optimizado**: Metadatos específicos para cada red social
- **Creación automática**: Se generan automáticamente para contenido largo
- #### 3. No se crean enlaces automáticamente
- Verificar `ENLACES_CORTOS_HABILITADO=true` en `.env`
- Comprobar que los model events están funcionando
- Revisar logs en `storage/logs/laravel.log`efijos semánticos**: Diferentes prefijos según el tipo de contenido
- **Estadísticas**: Integración con Google Analytics
- **Multidominio**: Soporte para múltiples dominios cortos
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

### 1. Detección Automática de Bots Sociales
El sistema utiliza la librería profesional **jaybizzle/crawler-detect** para identificar bots de redes sociales:

- **Usuarios normales:** Reciben redirección 301 directa a la URL original
- **Bots sociales** (Facebook, WhatsApp, Twitter, LinkedIn, etc.): Reciben página HTML con metadatos SEO completos

### 2. Gestión Inteligente de Contadores
- Los clics de bots sociales **NO** se cuentan en las estadísticas
- Solo se registran clics de usuarios reales
- Integración opcional con Google Analytics Measurement Protocol

### 3. Arquitectura DRY (Don't Repeat Yourself)
- **EnlaceCortoController**: Capa HTTP ultra-simplificada (86 líneas)
- **EnlaceCortoService**: Lógica de negocio centralizada
- **ContenidoHelper**: Gestión automática de sincronización SEO
- **Contenido Model**: Eventos boot() integrados (sin observers separados)

#### Obtener Enlace Corto (Crea si no existe)
```http
POST /enlaces-cortos/obtener
Authorization: Bearer {token}
Content-Type: application/json

{
    "url": "https://example.com/very/long/url",
    "prefijo": "e"
## 🎯 SEO y Metadatos Sociales

### Extracción Automática de Metadatos

Cuando se crea un enlace corto, el sistema extrae automáticamente:

- **Título**: De la etiqueta `<title>` o meta title
- **Descripción**: De meta description u Open Graph description  
- **Imagen**: De Open Graph image o primera imagen del contenido
- **Keywords**: De meta keywords si están disponibles

### Página de Vista Previa para Bots

```html
<!-- /resources/views/enlaces-cortos/preview.blade.php -->
<!DOCTYPE html>
<html lang="es">
<head>
    <title>{{ $enlace->titulo }}</title>
    <meta name="description" content="{{ $enlace->meta_descripcion }}">
    
    <!-- Open Graph -->
    <meta property="og:title" content="{{ $enlace->og_titulo }}">
    <meta property="og:description" content="{{ $enlace->og_descripcion }}">
    <meta property="og:image" content="{{ $enlace->og_imagen }}">
    <meta property="og:url" content="{{ $enlace->url_original }}">
    
    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $enlace->og_titulo }}">
    <meta name="twitter:description" content="{{ $enlace->og_descripcion }}">
    <meta name="twitter:image" content="{{ $enlace->og_imagen }}">
</head>
</html>
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
import { useEnlacesCortos } from '@/composables/useEnlacesCortos'

const {
    acortarParaCompartir, // Función principal - la única que necesitas en 99% casos
    obtenerEnlace,        // Función base que llama el backend
    cargando,             // Estado de carga reactivo
    error                 // Error reactivo si hay problemas
} = useEnlacesCortos()

// Uso típico en Share.vue
const prepararEnlaceCorto = async () => {
    const urlCorta = await acortarParaCompartir(fullUrl.value)
    if (urlCorta !== fullUrl.value) {
        currentUrl.value = urlCorta // URL se acortó
    }
    // Si no se acortó, currentUrl mantiene la original
}
```

### Integración en Componente Share.vue

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
    // Auto-acortar en segundo plano mientras usuario elige plataforma
    prepararEnlaceCorto()
}

const prepararEnlaceCorto = async () => {
    const urlCorta = await acortarParaCompartir(currentUrl.value)
    if (urlCorta !== currentUrl.value) {
        currentUrl.value = urlCorta
    }
}
</script>
```

## 📊 Estadísticas

### Google Analytics Integración

Cada evento relacionado con enlaces cortos se trackea automáticamente en Google Analytics:

#### Eventos Trackeados

1. **Clics en enlaces cortos**: `enlace_corto_click`
2. **Creación de enlaces**: `enlace_corto_creado`
3. **Reutilización de enlaces**: `enlace_corto_reutilizado`
4. **Consulta de estadísticas**: `enlaces_stats_consultadas`

#### Configuración Requerida

En tu archivo `.env`:
```env
GA_MEASUREMENT_ID=G-XXXXXXXXXX
GA_API_SECRET=your_measurement_protocol_secret
```

#### Tracking Automático

```php
// En cada clic de enlace corto
$analyticsController->trackEnlaceCorto($prefijo, $codigo, $url, request());

// En cada creación de enlace
AnalyticsHelper::trackCreacionEnlace($prefijo, $codigo, $urlOriginal, true);

// En reutilización de enlace existente
AnalyticsHelper::trackCreacionEnlace($prefijo, $codigo, $url, false);
```

### Métricas Básicas

- **Clics totales**: Contador por enlace (base de datos + GA)
- **Último clic**: Timestamp del último acceso
- **Enlaces más populares**: Top 10 por clics
- **Enlaces recientes**: Últimos 5 creados
- **Eventos en Google Analytics**: Tracking completo del comportamiento del usuario

### AnalyticsHelper

Utility class para tracking personalizado:

```php
use App\Pigmalion\AnalyticsHelper;

// Trackear evento personalizado
AnalyticsHelper::trackEventoPersonalizado('enlace_compartido', [
    'plataforma' => 'facebook',
    'tipo_contenido' => 'evento'
]);

// Trackear creación manual
AnalyticsHelper::trackCreacionEnlace('e', 'abc123', 'https://example.com/long-url');
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

### Comandos de Prueba

```powershell
# Simular bot de Facebook
curl -H "User-Agent: facebookexternalhit/1.1" http://localhost/e/jXPmfp

# Simular bot de WhatsApp
curl -H "User-Agent: WhatsApp/2.23.2.72" http://localhost/e/jXPmfp

# Simular usuario normal
curl -H "User-Agent: Mozilla/5.0" http://localhost/e/jXPmfp
```

### Respuestas Esperadas

- **Bots sociales**: HTML con metadatos SEO completos (200)
- **Usuarios**: Redirección 301 a URL original
- **Código inexistente**: Error 404

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

---

*Documentación actualizada: Septiembre 2025*
