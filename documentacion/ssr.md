# SSR (Server-Side Rendering) e Hidratación

## Arquitectura del Sistema SSR

La plataforma Tseyor.org utiliza Server-Side Rendering (SSR) combinado con hidratación del lado cliente para optimizar el rendimiento, SEO y experiencia de usuario. Esta implementación se basa en Inertia.js con Vue 3 y Laravel.

---

## 1. ¿Qué es SSR?

Server-Side Rendering (SSR) es una técnica donde el servidor genera el HTML completo de una página antes de enviarlo al navegador, en lugar de enviar un HTML vacío que luego se llena con JavaScript.

### Ventajas del SSR:
- **SEO mejorado**: Los motores de búsqueda pueden indexar el contenido completo
- **Performance inicial**: El usuario ve contenido inmediatamente sin esperar a que cargue JavaScript
- **Social sharing**: Las redes sociales pueden obtener metadatos completos de las páginas
- **Accesibilidad**: Funciona mejor con lectores de pantalla y navegadores antiguos

### Desventajas del SSR:
- **Complejidad**: Requiere sincronización entre servidor y cliente
- **Carga del servidor**: Mayor procesamiento en el servidor
- **Hidratación**: Necesidad de "hidratar" el HTML estático con funcionalidad interactiva

---

## 2. Hidratación en Vue 3 + Inertia.js

La hidratación es el proceso donde Vue.js toma el HTML renderizado por el servidor y lo "hidrata" convirtiéndolo en una aplicación Vue interactiva.

### Proceso de Hidratación:

1. **Renderizado del Servidor**: Laravel ejecuta el componente Vue en el servidor y genera HTML
2. **Envío al Cliente**: El navegador recibe HTML completo + JavaScript
3. **Hidratación**: Vue.js adjunta event listeners y reactividad al HTML existente
4. **Interactividad**: La página se vuelve completamente funcional

### Patrón Hydrated en Componentes

En componentes complejos, usamos el patrón "hydrated" para evitar mismatches entre SSR y cliente:

```vue
<script setup>
import { ref, onMounted } from 'vue'

const hydrated = ref(false)

onMounted(() => {
    hydrated.value = true
})
</script>

<template>
    <!-- Versión SSR simple -->
    <div v-if="!hydrated">
        <slot />
    </div>
    <!-- Versión cliente completa -->
    <div v-else class="componente-complejo">
        <slot />
        <!-- Elementos complejos que solo funcionan en cliente -->
    </div>
</template>
```

---

## 3. Implementación en Tseyor.org

### Configuración de Inertia.js SSR

```javascript
// vite.config.js
export default defineConfig({
  build: {
    ssr: 'bootstrap/ssr/ssr.js'
  }
})
```

```php
// config/inertia.php
'ssr' => [
    'enabled' => true,
    'url' => env('SSR_URL', 'http://localhost:13714'),
],
```

### Comandos para SSR

```bash
# Iniciar servidor SSR
php artisan inertia:start-ssr

# Detener servidor SSR
php artisan inertia:stop-ssr

# Construir para SSR
npm run build-all
```

### Componentes con Hidratación Especial

#### Image.vue
El componente Image usa hidratación para optimizar carga de imágenes:

```vue
<template>
    <!-- SSR: imagen básica -->
    <img v-if="!hydrated" :src="imageSrc" :alt="alt" />

    <!-- Cliente: componente completo con lazy loading -->
    <img v-else
         :src="displaySrc"
         :alt="alt"
         @load="imageLoaded = true"
         @error="errorLoading = true"
         :class="classes"
         :style="styles"
         ref="img" />
</template>
```

#### ActionButton.vue
El componente ActionButton evita mismatches de hidratación usando versiones diferentes:

```vue
<template>
    <!-- SSR: enlace simple -->
    <a v-if="!hydrated" :href="href" class="btn btn-primary">
        <slot />
    </a>

    <!-- Cliente: estructura completa con animaciones -->
    <a v-else :href="href" class="pushable relative inline group btn px-0">
        <!-- Estructura completa con sombras y animaciones -->
    </a>
</template>
```

---

## 4. Problemas Comunes y Soluciones

### Hydration Mismatch

**Síntoma**: Warning "Hydration children mismatch" en consola

**Causas comunes**:
- Componentes que renderizan diferente en servidor vs cliente
- Uso de `Date.now()`, `Math.random()`, o APIs del navegador en SSR
- Pseudo-elementos CSS (::before, ::after) que no existen en SSR
- Componentes asíncronos que cargan en diferentes momentos

**Soluciones**:
1. Usar el patrón `hydrated` para versiones diferentes
2. Evitar APIs del navegador durante SSR
3. Convertir pseudo-elementos en elementos DOM reales
4. Usar `onMounted` para lógica que requiere DOM

### Errores de Hidratación

```javascript
// Error común
[Vue warn]: Hydration node mismatch:
- rendered on server: <div>...</div>
- expected on client: <span>...</span>
```

**Solución**: Asegurar que el HTML inicial sea idéntico entre servidor y cliente.

### Debugging SSR

```bash
# Ver logs del servidor SSR
php artisan inertia:start-ssr --verbose

# Verificar que el HTML renderizado es correcto
curl http://localhost:13714/render
```

---

## 5. Mejores Prácticas

### Para Desarrolladores

1. **Probar siempre en SSR**: Verificar que las páginas funcionan tanto con SSR como sin él
2. **Evitar APIs del navegador**: Usar `onMounted` para código que requiere `window`, `document`, etc.
3. **Mantener consistencia**: El HTML inicial debe ser idéntico entre servidor y cliente
4. **Usar patrón hydrated**: Para componentes complejos que necesitan diferentes versiones

### Para Componentes

```vue
<script setup>
// ✅ Correcto: usar onMounted
const isClient = ref(false)
onMounted(() => {
    isClient.value = true
})

// ❌ Incorrecto: usar typeof window en computed
const isClient = computed(() => typeof window !== 'undefined')
</script>
```

### Para Estilos

- Evitar `@apply` y `@reference` que puedan causar problemas en SSR
- Usar clases CSS consistentes entre servidor y cliente
- Considerar `v-cloak` para ocultar contenido hasta hidratación completa

---

## 6. Performance y Optimización

### Lazy Hydration

La aplicación usa LazyHydrate para componentes pesados:

```vue
<LazyHydrate>
    <ComponentePesado />
</LazyHydrate>
```

Esto retrasa la hidratación hasta que el componente entra en viewport, mejorando el tiempo de carga inicial.

### Code Splitting

```javascript
// Componentes asíncronos
const ComponenteAsincrono = defineAsyncComponent(() =>
    import('./ComponenteAsincrono.vue')
)
```

### Bundle Analysis

```bash
# Analizar tamaño de bundles
npm run build -- --mode analyze
```

---

## 7. Troubleshooting

### Problema: Componente no se hidrata
**Solución**: Verificar que `onMounted` se ejecuta y que no hay errores de JavaScript

### Problema: Estilos diferentes en SSR vs cliente
**Solución**: Usar CSS consistente y evitar estilos dinámicos que dependan del DOM

### Problema: Errores de hidratación intermitentes
**Solución**: Implementar el patrón `hydrated` para componentes problemáticos

### Problema: SSR server no responde
**Solución**:
```bash
php artisan inertia:stop-ssr
php artisan inertia:start-ssr
```

---

## 8. Recursos Adicionales

- [Documentación oficial de Inertia.js SSR](https://inertiajs.com/server-side-rendering)
- [Guía de Vue 3 SSR](https://vuejs.org/guide/scaling-up/ssr.html)
- [Lazy Hydration en Vue](https://markus.oberlehner.net/blog/lazy-hydration-vue-components/)
- [Debugging SSR en Laravel](https://laravel.com/docs/ssr)
