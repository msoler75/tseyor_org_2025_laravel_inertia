# Configuración de Google Analytics 4 - TSEYOR.org

## Configuración Implementada

### 1. Variables de Entorno

Se han añadido las siguientes variables al archivo `.env.example`:

```bash
# Google Analytics 4 Configuration
GOOGLE_ANALYTICS_TRACKING_ID=
GOOGLE_ANALYTICS_MEASUREMENT_ID=
```

**Para configurar tu GA4:**
1. Copia estas variables a tu archivo `.env`
2. Rellena `GOOGLE_ANALYTICS_MEASUREMENT_ID` con tu ID de medición de GA4 (formato: G-XXXXXXXXXX)
3. Opcionalmente, rellena `GOOGLE_ANALYTICS_TRACKING_ID` si necesitas compatibilidad con Universal Analytics

### 2. Configuración Backend (Laravel)

- **Archivo:** `config/services.php`
- Se agregó la configuración de Google Analytics que hace las variables disponibles en toda la aplicación
- **Middleware:** `app/Http/Middleware/HandleInertiaRequests.php` - Las variables se comparten automáticamente con el frontend

### 3. Configuración Frontend (Vue.js + Inertia)

#### Composable Principal: `useGoogleAnalytics.js`

**Ubicación:** `resources/js/composables/useGoogleAnalytics.js`

**Funciones disponibles:**
- `loadGoogleAnalytics()` - Inicializa GA4
- `trackPageView(url, title)` - Seguimiento de páginas visitadas
- `trackEvent(eventName, parameters)` - Eventos personalizados
- `trackDownload(fileName, fileType)` - Descargas de archivos
- `trackSearch(searchTerm)` - Búsquedas realizadas
- `trackVideoPlay(videoTitle, videoUrl)` - Reproducción de videos
- `trackAudioPlay(audioTitle, audioUrl)` - Reproducción de audios
- `trackNewsletterSignup(method)` - Suscripciones a newsletter
- `trackContactForm(formName)` - Envío de formularios
- `trackUserEngagement(engagementType, content)` - Interacciones del usuario
- `grantConsent()` - Conceder consentimiento de cookies
- `denyConsent()` - Denegar consentimiento de cookies

#### Inicialización Automática

**Archivo:** `resources/js/app.js`
- GA4 se inicializa automáticamente al cargar la aplicación
- El seguimiento de navegación entre páginas está configurado automáticamente para aplicaciones SPA con Inertia

### 4. Eventos Configurados Automáticamente

#### Navegación de Páginas (SPA/Inertia.js)
- **Archivo:** `resources/js/app.js`
- Se trackean automáticamente todas las navegaciones entre páginas
- **Compatible con aplicaciones SPA** (Single Page Application)
- **Eventos utilizados:** 
  - Navegación: Evento `finish` de Inertia.js (más preciso que `navigate`)
- **Protección anti-duplicados:**
  - Control de URL repetidas en `app.js` con timeout de 100ms
  - Control de tiempo en `useGoogleAnalytics.js` con debounce de 1 segundo
- **Datos enviados:** URL completa, título de página, y path relativo
- **Logging:** Activado en consola para debugging, incluye prevención de duplicados

#### Búsquedas
- **Archivo:** `resources/js/Components/GlobalSearch.vue`
- Se trackean automáticamente todas las búsquedas realizadas en el sitio

#### Enlaces Externos y Descargas
- **Archivo:** `resources/js/Components/Link.vue`
- Se trackean automáticamente los clics en enlaces externos, emails y teléfonos
- **Archivo:** `resources/js/app.js`
- **Tracking automático de descargas**: Se detectan automáticamente todas las descargas de archivos (PDF, DOC, XLS, ZIP, MP3, MP4, etc.) tanto en elementos `<a download>` como en componentes `Link`

#### Reproducción de Audio/Video
- **Archivo:** `resources/js/Stores/player.js`
- Se trackea automáticamente cuando se reproduce un audio o video

#### Formularios de Contacto y Suscripciones
- **Archivo:** `resources/js/Pages/Contactar.vue`
- Se trackea automáticamente el envío exitoso de formularios de contacto
- **Archivo:** `resources/js/Components/Suscribe.vue`
- Se trackea automáticamente las suscripciones al boletín desde cualquier página
- **Archivo:** `resources/js/Pages/Profile/Partials/BoletinSuscripcion.vue`
- Tracking específico para suscripciones desde el perfil de usuario

## Tracking Automático de Descargas

### ✅ Funcionamiento Automático

El sistema detecta y trackea automáticamente **TODAS** las descargas sin necesidad de configuración adicional:

**Tipos de archivo detectados:**
- Documentos: `.pdf`, `.doc`, `.docx`, `.xls`, `.xlsx`, `.ppt`, `.pptx`
- Archivos comprimidos: `.zip`, `.rar`
- Multimedia: `.mp3`, `.mp4`, `.avi`

**Métodos de enlace compatibles:**
```html
<!-- Elemento <a> con download (como en libros) -->
<a href="libro.pdf" download class="btn">Descargar</a>

<!-- Componente Link -->
<Link href="documento.pdf">Descargar PDF</Link>

<!-- Enlaces automáticos (por extensión) -->
<a href="archivo.zip">Descargar ZIP</a>
```

**Datos enviados a GA4:**
- Evento: `file_download`
- Nombre del archivo: `libro.pdf`
- Tipo de archivo: `pdf`
- URL completa del archivo: `https://tseyor.org/storage/libros/libro.pdf`
- Página desde donde se descargó: URL y título de la página actual

### 🔍 Verificar en Google Analytics

1. Ve a **Tiempo real** → **Eventos**
2. Busca eventos `file_download`
3. Ve a **Informes** → **Eventos** → **file_download** para estadísticas completas

## Cómo Usar

### Tracking Básico (Automático)
No necesitas hacer nada especial. Los siguientes eventos se trackean automáticamente:
- Visitas a páginas
- Búsquedas en el sitio
- Clics en enlaces externos
- **Descargas de archivos** (PDF, DOC, XLS, ZIP, MP3, MP4, etc.)
- Reproducción de audio/video
- Envío de formularios de contacto
- **Suscripciones al boletín** (desde cualquier página: portada, footer, perfil)

### Tracking Personalizado

Para añadir tracking personalizado a cualquier componente Vue:

```javascript
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js';

export default {
  setup() {
    const { trackEvent, trackDownload, trackUserEngagement } = useGoogleAnalytics();
    
    // Ejemplo: Trackear descarga de archivo
    const downloadFile = (fileName) => {
      trackDownload(fileName, 'pdf');
    };
    
    // Ejemplo: Trackear evento personalizado
    const customAction = () => {
      trackEvent('custom_action', {
        action_category: 'user_interaction',
        value: 1
      });
    };
    
    // Ejemplo: Trackear engagement
    const userInteracted = () => {
      trackUserEngagement('scroll', 'home_page');
    };
    
    return {
      downloadFile,
      customAction,
      userInteracted
    };
  }
};
```

### Consentimiento de Cookies (GDPR)

Si necesitas implementar consentimiento de cookies:

```javascript
import { useGoogleAnalytics } from '@/composables/useGoogleAnalytics.js';

const { grantConsent, denyConsent } = useGoogleAnalytics();

// Cuando el usuario acepta cookies
function acceptCookies() {
  grantConsent();
}

// Cuando el usuario rechaza cookies
function rejectCookies() {
  denyConsent();
}
```

## Eventos Específicos del Sitio

### Para Inscripciones a Eventos/Cursos
```javascript
const { trackEvent } = useGoogleAnalytics();

trackEvent('sign_up', {
  method: 'curso_inscripcion',
  event_name: 'Nombre del Curso',
  value: 1
});
```

### Para Descargas de Libros/PDFs
```javascript
// YA NO ES NECESARIO - SE TRACKEA AUTOMÁTICAMENTE
// Cualquier enlace a PDF, DOC, etc. se trackea automáticamente
// Ejemplo: <a href="libro.pdf" download>Descargar</a>
// O usando el componente: <Link href="libro.pdf">Descargar</Link>

// Solo usar si necesitas tracking personalizado:
const { trackDownload } = useGoogleAnalytics();
trackDownload('nombre_libro.pdf', 'libro', 'https://tseyor.org/storage/libros/libro.pdf');
```

**Información trackeada automáticamente:**
- ✅ Nombre del archivo
- ✅ Tipo/extensión del archivo  
- ✅ URL completa del archivo
- ✅ Página desde donde se descargó
- ✅ Título de la página actual

### Para Suscripciones al Boletín
```javascript
// YA NO ES NECESARIO - SE TRACKEA AUTOMÁTICAMENTE
// El componente Suscribe trackea automáticamente todas las suscripciones
// Funciona en: Portada, Footer, Perfil de usuario, etc.

// Solo usar si necesitas tracking personalizado:
const { trackNewsletterSignup } = useGoogleAnalytics();
trackNewsletterSignup('boletin_tseyor');
```

**Métodos de suscripción trackeados:**
- ✅ Desde la portada: `boletin_tseyor`
- ✅ Desde el perfil de usuario: `boletin_tseyor_profile`  
- ✅ Desde el footer: `boletin_tseyor`
- ✅ Desde cualquier otra página: `boletin_tseyor`

## Debugging y Verificación SPA

### 🔍 Para verificar tracking en aplicación SPA:

1. **Consola del navegador:** 
   - Abre F12 → Console
   - Navega por el sitio y verás logs como:
     ```
     Inertia navigation finished, tracking page view: https://tseyor.org/libros Libros - TSEYOR.org
     trackPageView called with: https://tseyor.org/libros Libros - TSEYOR.org
     ```
   - Si hay duplicados, verás:
     ```
     Duplicate page view prevented: https://tseyor.org/libros
     ```

2. **Google Analytics tiempo real:**
   - Ve a tu panel GA4 → Tiempo real → Vista general
   - Navega por páginas y verás actualización inmediata **sin duplicados**
   - Ve a Tiempo real → Eventos para ver `page_view` events

3. **Verificación específica SPA:**
   - Cada navegación con Inertia.js debe generar **un solo** `page_view`
   - El `page_path` debe actualizarse correctamente
   - No debe haber recargas de página completa

### ⚠️ Protección anti-duplicados implementada:

- **Nivel 1 (app.js):** Timeout de 100ms para agrupar eventos múltiples
- **Nivel 2 (composable):** Debounce de 1 segundo para misma URL
- **Control de estado:** Tracking de última URL y timestamp
- **Logging:** Mensajes específicos cuando se previenen duplicados

## Verificación

Para verificar que GA4 está funcionando:

1. **Consola del navegador:** Los eventos se loggean automáticamente si GA4 está configurado
2. **Google Analytics:** Ve a Tiempo real > Eventos en tu panel de GA4
3. **Google Tag Assistant:** Extensión de Chrome para verificar la implementación

## Mantenimiento

### Añadir Nuevos Eventos

1. Si necesitas nuevos tipos de eventos, modifica el composable `useGoogleAnalytics.js`
2. Para eventos específicos de componentes, simplemente importa y usa las funciones existentes

### Debugging

Si GA4 no funciona:
1. Verifica que `GOOGLE_ANALYTICS_MEASUREMENT_ID` esté configurado en `.env`
2. Abre la consola del navegador y busca errores de Google Analytics
3. Verifica que el ID de medición tenga el formato correcto (G-XXXXXXXXXX)

## Notas Importantes

- **SSR Compatible:** La configuración funciona tanto con renderizado del lado del servidor como del cliente
- **Performance:** El script de GA4 se carga de forma asíncrona para no afectar el rendimiento
- **Privacidad:** Se puede configurar el consentimiento de cookies según las regulaciones GDPR
- **Eventos Automáticos:** La mayoría de eventos importantes ya están configurados automáticamente
- **Extensible:** Fácil de extender con nuevos eventos personalizados según las necesidades del sitio

## Enlaces Útiles

- [Documentación GA4](https://developers.google.com/analytics/devguides/collection/ga4)
- [Eventos recomendados GA4](https://developers.google.com/analytics/devguides/collection/ga4/reference/events)
- [Google Tag Assistant](https://support.google.com/tagassistant/answer/2947093)
