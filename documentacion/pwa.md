# 🚀 Configuración PWA - Tseyor

## 📍 Ubicaciones de archivos

### Configuración y lógica principal
- **`/resources/js/composables/usePWA.js`** - Composable que maneja la lógica PWA y configuración

### Componentes visuales
- **`/resources/js/Components/PWANotifications.vue`** - Componente visual de los popups
- **`/resources/js/Layouts/AppLayout.vue`** - Incluye las notificaciones en el layout

## ⚙️ Configuración de popups

### Para activar/desactivar popups individualmente:

Edita `/resources/js/composables/usePWA.js`, en la constante `PWA_CONFIG`:

```javascript
notifications: {
  install: true,   // "¡Instala Tseyor!" - Banner de instalación
  update: true,    // "Nueva versión disponible" - Notificación de actualización  
  offline: true,   // "App lista offline" - Confirmación de funcionalidad offline
}
```

### Para cambiar tiempos de auto-ocultación:

```javascript
autoHideDelay: {
  update: 5000,    // 5 segundos para notificación de actualización
  offline: 3000    // 3 segundos para notificación offline
}
```

### Para habilitar PWA en desarrollo:

```javascript
enableInDev: true  // Por defecto: false (solo producción)
```

## 🎯 Casos de uso comunes

### Desactivar TODOS los popups:
```javascript
notifications: {
  install: false,
  update: false, 
  offline: false,
}
```

### Solo mostrar actualizaciones:
```javascript
notifications: {
  install: false,
  update: true,   // ✅ Solo este
  offline: false,
}
```

### Solo mostrar instalación:
```javascript
notifications: {
  install: true,   // ✅ Solo este
  update: false,
  offline: false,
}
```

## 🔧 Configuración centralizada

✅ **Todo en un solo archivo**: `/resources/js/composables/usePWA.js`  
✅ **Configuración al inicio**: Constante `PWA_CONFIG`  
✅ **Beneficio**: Cambios centralizados, menos archivos, más mantenible

## 📱 Tipos de notificaciones

1. **Banner de instalación** (`install`): Aparece cuando la PWA es instalable
2. **Actualización disponible** (`update`): Se muestra cuando hay nueva versión 
3. **App offline ready** (`offline`): Confirma que la app funciona sin conexión

## 🚫 Para desactivar completamente las notificaciones PWA

Comenta esta línea en `/resources/js/Layouts/AppLayout.vue`:
```vue
<!-- <PWANotifications /> -->
```

## 🔧 Configuración avanzada

### Intervalo de verificación de actualizaciones:
```javascript
updateCheckInterval: 60000, // 1 minuto (en milisegundos)
```

### Configuración futura de prompts de instalación:
```javascript
installPrompt: {
  minVisitTime: 30000,  // 30 segundos antes de mostrar prompt
  minVisits: 3          // Número mínimo de visitas
}
```

## 🛠️ Estructura del código

```
/resources/js/
├── composables/
│   └── usePWA.js              ← Configuración + lógica PWA
├── Components/
│   └── PWANotifications.vue   ← UI de los popups
└── Layouts/
    └── AppLayout.vue          ← Incluye <PWANotifications />
```

---

# 🌐 Configuración PWA Multi-dominio

## 📍 Situación actual
- **tseyor.org** - Sitio principal (Laravel + Inertia + Vue)
- **puzle.tseyor.org** - Juego puzzle (aplicación web independiente)

## ⚠️ Problemas identificados en configuración actual

### 1. Scope y Start URL conflictivos
```javascript
// ❌ ACTUAL (problemático para subdominios)
scope: '/',
start_url: '/',
```

### 2. Service Worker con mismo nombre
```javascript
// ❌ ACTUAL (conflicto potencial)
filename: 'sw.js',
```

### 3. Manifest genérico
```javascript
// ❌ ACTUAL (caché cruzado posible)
manifestFilename: 'pwa-manifest.json',
```

## ✅ Configuración recomendada

### Para tseyor.org (sitio principal)
```javascript
VitePWA({
  registerType: 'autoUpdate',
  filename: 'tseyor-sw.js',                    // ✅ Específico
  manifestFilename: 'tseyor-manifest.json',   // ✅ Específico
  workbox: {
    globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff,woff2,ttf,eot}']
  },
  manifest: {
    name: 'Tseyor.org',
    short_name: 'Tseyor',
    id: 'org.tseyor.main',                     // ✅ ID único
    description: 'TSEYOR - Preparándonos para el Salto Cuántico y la creación de las Sociedades Armónicas',
    theme_color: '#1e40af',
    background_color: '#ffffff',
    display: 'standalone',
    orientation: 'portrait-primary',
    scope: '/',                                // ✅ OK para dominio principal
    start_url: '/',                           // ✅ OK para dominio principal
    // ... iconos
  }
})
```

### Para puzle.tseyor.org (aplicación puzzle)
```javascript
VitePWA({
  registerType: 'autoUpdate',
  filename: 'puzle-sw.js',                     // ✅ Específico
  manifestFilename: 'puzle-manifest.json',    // ✅ Específico  
  workbox: {
    globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff,woff2,ttf,eot}']
  },
  manifest: {
    name: 'Puzle Tseyor',
    short_name: 'Puzle',
    id: 'org.tseyor.puzle',                   // ✅ ID único DIFERENTE
    description: 'Juego de Puzzle de Tseyor',
    theme_color: '#059669',                   // ✅ Color diferente
    background_color: '#ffffff',
    display: 'standalone',
    orientation: 'portrait-primary',
    scope: '/',                               // ✅ OK (en SU subdominio)
    start_url: '/',                          // ✅ OK (en SU subdominio)
    // ... iconos específicos del puzzle
  }
})
```

## 🔧 Cambios necesarios en tseyor.org

### 1. Actualizar vite.config.js
Cambiar el VitePWA a:

```javascript
VitePWA({
  registerType: 'autoUpdate',
  filename: 'tseyor-sw.js',                    // ✅ CAMBIO
  manifestFilename: 'tseyor-manifest.json',   // ✅ CAMBIO
  workbox: {
    globPatterns: ['**/*.{js,css,html,ico,png,svg,webp,woff,woff2,ttf,eot}']
  },
  manifest: {
    name: 'Tseyor.org',
    short_name: 'Tseyor',
    id: 'org.tseyor.main',                     // ✅ NUEVO
    description: 'TSEYOR - Preparándonos para el Salto Cuántico y la creación de las Sociedades Armónicas',
    theme_color: '#1e40af',
    background_color: '#ffffff',
    display: 'standalone',
    orientation: 'portrait-primary',
    scope: '/',                                
    start_url: '/',                           
    icons: [
      // ... mantener iconos actuales
    ]
  },
  devOptions: {
    enabled: false
  }
})
```

### 2. Verificar en navegador
Después del cambio, verificar en DevTools → Application:
- **Manifest**: Debe mostrar `tseyor-manifest.json`
- **Service Workers**: Debe mostrar `tseyor-sw.js`  
- **Storage**: Limpiar datos anteriores si es necesario

## 🎯 Beneficios de esta configuración

✅ **Independencia total**: Cada PWA funciona independientemente  
✅ **Sin conflictos**: Service workers no interfieren entre sí  
✅ **Instalación limpia**: Usuario puede instalar ambas apps  
✅ **Caché separado**: Cada app mantiene su propio caché  
✅ **Identificación única**: IDs únicos previenen confusión del navegador

## 🚨 Notas importantes

1. **Aplicar cambios en producción**: Estos cambios requieren nuevo build y deploy
2. **Limpiar caché**: Usuarios existentes podrían necesitar limpiar datos de app
3. **Testing**: Probar instalación en diferentes dispositivos
4. **Icons**: Considerar iconos diferentes para cada app (opcional pero recomendado)

## 📱 Resultado esperado

- Usuario puede instalar "Tseyor" desde tseyor.org  
- Usuario puede instalar "Puzle Tseyor" desde puzle.tseyor.org
- Ambas apps funcionan independientemente
- No hay conflictos ni interferencias
