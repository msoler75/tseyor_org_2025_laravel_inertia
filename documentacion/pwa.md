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
