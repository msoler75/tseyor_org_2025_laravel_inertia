# Estudio de Transiciones en TSEYOR.org

## Introducción

Este estudio analiza el sistema de transiciones de página en la plataforma TSEYOR.org, específicamente los mecanismos de fadeout durante la navegación, scroll automático y el componente ContentMain. El objetivo es entender la lógica actual implementada en `transitionPages.js` y proponer simplificaciones para reducir la complejidad.

## Fadeout en Navegación

### Propiedad `fadeOut` en Link.vue

La propiedad `fadeOut` en el componente `Link.vue` permite forzar un fadeout de página cuando se hace clic en un enlace. Cuando se establece en `true`, ejecuta `nav.fadeoutPage()` y automáticamente setea `nav.dontFadeout = true`.

**Uso actual:**
- Principalmente usado en componentes internos como `Back.vue`, que pasa la prop `fadeOut` al `Link`.
- No se encontró uso directo en páginas con `fadeOut="true"` o `:fadeOut="true"`.

**Archivos relacionados:**
- `resources/js/Components/Link.vue` (líneas 102, 134-137)
- `resources/js/Components/Back.vue` (línea 5, pasa `:fadeOut="fadeOut"`)

### Lógica automática de fadeout en transitionPages.js

En `transitionPages.js`, hay lógica automática que decide cuándo hacer fadeout basado en la ruta de destino:

```javascript
const fadeoutWhenNavigateTo = /^\/(audios|videos|comunicados|contactos|entradas|equipos|eventos|experiencias|informes|libros|meditaciones|psicografias|ong|utg|normativas|noticias|publicaciones|usuarios|preguntas-frecuentes|confederacion|el-rayo)/;
if (fadeoutWhenNavigateTo.exec(nuevaRuta.pathname)) {
    nav.fadeoutPage();
}
```

Y adicionalmente para rutas específicas:
```javascript
const fadeoutWhenNavigateTo = /^\/(comunicados|centros).*/;
if (fadeoutWhenNavigateTo.exec(nuevaRuta.pathname)) {
    nav.fadeoutPage();
}
```

## Componente FadeOnNavigate

El componente `FadeOnNavigate.vue` aplica automáticamente `opacity-0 pointer-events-none` cuando `nav.navigating` es `true`, con una transición de 200ms.

**Uso actual:**
- `resources/js/Pages/Libros/Index.vue` (línea 43): Envuelve el contenido de resultados de búsqueda
- `resources/js/Pages/Guias/Index.vue` (línea 53): Envuelve el contenido de resultados

**Implementación:**
```vue
<template>
    <component :is="as" class="transition-opacity duration-200"
        :class="useNav().navigating?'opacity-0 pointer-events-none':''">
        <slot />
    </component>
</template>
```

## Props de useNav
 
### preservePage

Propiedad en `Link.vue` que setea `nav.dontFadeout = true` cuando es `true`, preservando el estado de la página actual durante la navegación.

## Componente ContentMain

El componente `ContentMain.vue` envuelve el contenido principal de las páginas y puede tener `fade-on-navigate` (default: true).

**Implementación:**
```vue
<template>
    <div id="content-main" class="transition-opacity duration-200"
        :class="fadeOnNavigate && nav.navigating?'opacity-0 pointer-events-none':''">
        <slot/>
    </div>
</template>
```

### Uso con fade-on-navigate="false"

- `resources/js/Pages/Libros/Index.vue` (línea 36): `:fade-on-navigate="false"`
- `resources/js/Pages/Guias/Index.vue` (línea 48): `:fade-on-navigate="false"`

### Uso con fade-on-navigate (sin especificar, default true)

- `resources/js/Pages/Terminos/Termino.vue` (línea 63): `fade-on-navigate`
- `resources/js/Pages/Terminos/Index.vue` (línea 64): `fade-on-navigate`

### Uso sin fade-on-navigate (default true)

Múltiples páginas usan `ContentMain` sin especificar `fade-on-navigate`, por lo que aplican fade por defecto:
- `resources/js/Pages/Videos/Index.vue`
- `resources/js/Pages/Psicografias/Index.vue`
- `resources/js/Pages/Noticias/Index.vue`
- `resources/js/Pages/Meditaciones/Index.vue`
- `resources/js/Pages/Guias/Guia.vue`
- `resources/js/Pages/Lugares/Index.vue`
- `resources/js/Pages/Preguntas/Index.vue`

## Lógica en transitionPages.js

La lógica actual es compleja y tiene múltiples condiciones:

1. **Detección de sección igual:** Compara si la nueva ruta está en la misma sección (primer segmento de path)
2. **Fadeout automático:** Basado en regex de rutas
3. **Scroll condicional:** Dependiendo de si es misma página/sección, `nav.dontScroll`, `nav.dontFadeout`

**Problemas identificados:**
- Lógica enrevesada con múltiples flags (`dontFadeout`, `dontScroll`, `fadingOutPage`)
- Regex hardcodeadas que pueden no cubrir todos los casos
- Mezcla de lógica automática con overrides manuales
- Dificultad para predecir el comportamiento en rutas específicas

## Análisis y Propuestas de Simplificación

### Problemas Actuales

1. **Complejidad:** La lógica en `transitionPages.js` tiene muchas condiciones anidadas y flags que interactúan de formas no obvias.

2. **Inconsistencia:** Algunas páginas usan `FadeOnNavigate`, otras `ContentMain` con `fade-on-navigate="false"`, otras confían en la lógica automática.

3. **Mantenibilidad:** Cambiar el comportamiento requiere modificar regex y lógica en múltiples lugares.

4. **Predecibilidad:** Es difícil saber qué pasará al navegar a una ruta sin revisar el código.

### Propuestas de Simplificación

#### Opción 1: Sistema Declarativo por Página

Crear un objeto de configuración por ruta que defina el comportamiento:

```javascript
const pageTransitions = {
    '/libros': { fadeout: false, scroll: 'content' },
    '/libros/*': { fadeout: true, scroll: 'top' },
    '/comunicados': { fadeout: true, scroll: 'none' },
    // ...
};
```

#### Opción 2: Simplificar Componentes

- Unificar `FadeOnNavigate` y `ContentMain` fade-on-navigate en un solo mecanismo
- Usar props más claras en los componentes

#### Opción 3: Lógica Basada en Layout

Asociar comportamientos por layout o sección en lugar de rutas individuales.

#### Opción 4: Configuración Centralizada

Crear un archivo de configuración que defina transiciones por patrón de ruta, reemplazando la lógica automática.

### Recomendación

Implementar **Opción 1** con un sistema declarativo que permita definir por ruta:
- `fadeout`: `true/false/auto`
- `scroll`: `'top'/'content'/'none'/'hash'`

Esto simplificaría `transitionPages.js` y haría el comportamiento más predecible y fácil de mantener.

## Conclusión

El sistema actual funciona pero es complejo. Una simplificación declarativa reduciría la lógica enrevesada y mejoraría la mantenibilidad, permitiendo efectos de fadeout y scroll más consistentes y configurables por ruta.</content>
<parameter name="filePath">d:\projects\tseyor\laravel_inertia\documentacion\transiciones.md
