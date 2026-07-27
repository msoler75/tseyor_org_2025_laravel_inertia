# Auditoría Técnica UX/UI — ONG Index.vue

## Audit Health Score (5 dimensiones, /20)

| Dimensión | Puntuación | Evaluación breve |
|-----------|-----------|------------------|
| **Accesibilidad (WCAG 2.1)** | 2/4 | Estructura semántica básica presente, pero carencias graves en contraste, focus y navegación por teclado. |
| **Rendimiento percibido** | 2/4 | Sin lazy loading ni skeleton screens. Las imágenes JPG pueden bloquear el paint. Carga síncrona de todo el contenido. |
| **Responsive / Mobile UX** | 3/4 | Grid responsive correcto. El diseño se adapta, pero la experiencia sigue siendo lineal sin navegación adaptativa. |
| **HTML semántico / ARIA** | 2/4 | Uso genérico de `div` y `p` sin landmarks, regiones ni roles ARIA. Artículos con `<article>` bien usados (línea 112). |
| **Consistencia visual** | 3/4 | Patrón unificado de colores, tipografía y espaciado. El problema es que es demasiado consistente — no hay diferenciación. |

**Total: 12/20** — Riesgo moderado. El sitio es funcional pero no superaría una auditoría WCAG AA completa.

---

## Anti-Patterns Verdict

| Anti-Patrón | Tipo | Severidad | Ubicación |
|------------|------|-----------|-----------|
| **Layout monocromo sin pausas visuales** | Diseño visual | Alta | Secciones enteras (líneas 10-189) — mismo bg, mismo padding, mismo ancho |
| **Contenido estático como datos de frontend** | Arquitectura | Alta | `ambitos` y `proyectos` hardcodeados en `<script setup>` (líneas 208-244) |
| **Logo raster en JPG para un asset de marca** | Performance / Calidad | Media | Línea 13 — `logo-ong-mundo-armonico.JPG` |
| **Sin estados de carga en CTAs** | UX | Alta | Líneas 38, 42, 148, 174, 181 — todos sin loading ni disabled state |
| **Enlaces mixtos literal/route()** | Mantenibilidad | Baja | Líneas 38, 42, 181 usan strings; línea 174 usa `route()` |

---

## Executive Summary

La página ONG Index.vue es **funcional pero inmadura**. Cumple como presentación institucional básica, pero carece de refinamiento en tres áreas críticas: **accesibilidad profunda** (falta de landmarks, focus visible, contraste), **rendimiento percibido** (sin lazy loading, skeleton states, ni feedback de navegación) y **semántica estructural** (exceso de `div` genéricos, nulos roles ARIA). 

El mayor hallazgo sistémico es que el contenido institucional clave (ámbitos de acción, proyectos) está **hardcodeado en el frontend** en lugar de servirse desde el backend o CMS, lo que implica editar código para actualizarlo. Esto contradice el propósito de una plataforma CMS.

---

## Detailed Findings by Severity

### 🔴 Alta

#### F1 — Datos de contenido hardcodeados (líneas 208-244)
**Hallazgo**: `ambitos` y `proyectos` son arrays estáticos en `<script setup>`. Para añadir, modificar o eliminar un ámbito o proyecto, un editor debe tocar código Vue.
**Impacto**: Mantenibilidad cero para el equipo de contenido. Cualquier cambio requiere deploy.
**Sugerencia**: Migrar a props servidas desde el controlador Laravel, o al menos a un archivo de datos externo (JSON, config).

#### F2 — Inexistencia de estados de interacción en CTAs (líneas 38, 42, 148, 174, 180)
**Hallazgo**: Ningún botón tiene `:disabled` durante navegación, ni muestra loading spinner, ni estado de error. Inertia proporciona navegación progresiva, pero no hay feedback visual.
**Impacto**: Doble-click accidental en "Donar ahora" o "Hazte voluntario". Percepción de lentitud.
**Sugerencia**: Botones deberían mostrar `loading` state mientras Inertia navega: `:loading="form.processing"` o similar.

#### F3 — Sin navegación por teclado funcional (sistémico)
**Hallazgo**: No hay `tabindex`, `aria-current="page"`, ni skip-to-content. El NavBar que envuelve la página (desde AppLayout) puede tener algo, pero la página en sí no declara landmarks.
**Impacto**: Usuarios de teclado/lector de pantalla deben tabular por todo el contenido sin atajos.
**Sugerencia**: Añadir `<nav aria-label="Secciones">` con jump-links y `main` role.

### 🟡 Media

#### F4 — Logo JPG sin optimización ni vector (línea 13)
**Hallazgo**: `logo-ong-mundo-armonico.JPG` — extensión en mayúsculas, formato JPG con pérdida, sin versiones 2x ni WebP.
**Impacto**: En displays retina (>200 DPI) el logo se ve borroso. El peso del JPG puede ser innecesariamente alto.
**Sugerencia**: Convertir a SVG o WebP. Servir con `srcset` para retina. Usar componente `<Image>` del proyecto si existe.

#### F5 — Labels redundantes y con tracking arbitrario (líneas 18, 58, 98, 123, 159)
**Hallazgo**: El patrón `inline-block text-xs font-bold uppercase tracking-[0.28em] text-primary/60 mb-5 font-display` se repite idéntico 5 veces. El valor `0.28em` es arbitrario.
**Impacto**: Violación DRY mantenible. Si el equipo decide cambiar el estilo de labels, hay que editar 5 lugares.
**Sugerencia**: Extraer a un componente `<SectionLabel>` o clase utilitaria global.

#### F6 — Anchor `#proyectos` sin smooth scroll visible (líneas 48, 97)
**Hallazgo**: El link `Conocer nuestros proyectos →` navega a `#proyectos` que tiene `scroll-mt-24`. No se ve transición ni feedback de que el scroll ocurrió.
**Impacto**: El usuario puede pensar que la página no respondió.
**Sugerencia**: Inertia supports scroll behavior. Asegurar `scroll-behavior: smooth` en CSS global o usar JS para indicar visualmente el destino.

### 🟢 Baja

#### F7 — Unicode `→` como indicador visual (línea 49)
**Hallazgo**: Carácter `→` (U+2192) puede renderizar distinto según el sistema del usuario.
**Impacto**: Inconsistencia visual menor.
**Sugerencia**: Usar el componente `<ArrowRight />` ya importado de `lucide-vue-next`.

#### F8 — Rutas mixtas (líneas 38, 42, 174, 181)
**Hallazgo**: `/donar`, `/centros`, `/libros/...` son strings planas; `route('cursos.inscripcion.nueva')` usa named route.
**Impacto**: Si cambia la estructura de rutas, las strings planas se rompen silenciosamente.
**Sugerencia**: Unificar todo a `route()`.

---

## Patterns & Systemic Issues

### Patrón 1: Render-via-loop sin estado interactivo
Los bloques de ámbitos (líneas 72-75) y proyectos (líneas 112-115) son `<div>` con `v-for` + `divide-y`. Cada item no es clickeable, no tiene `:key` robusto (usa `ambito.titulo` que puede duplicarse), y no hay distinción visual entre items. Si en el futuro estos items necesitan expandirse o navegar, habrá que reescribir toda la sección.

### Patrón 2: Override de DaisyUI sin tema definido
Los botones `rounded-full px-8 shadow-lg` sobreescriben defaults de DaisyUI de forma inconsistente (líneas 38, 148, 175) vs `rounded-full px-8` (línea 42) vs `rounded-full bg-base-100 border border-base-300 hover:border-primary/40 px-8` (línea 182). Tres estilos de botón distintos para acciones similares.

### Patrón 3: Espaciado uniforme sin jerarquía visual
Todas las secciones usan `py-16 md:py-24`. La primera sección (hero) debería tener más padding superior, las intermedias podrían tener menos, la última más. La uniformidad aplana la jerarquía visual.

### Patrón 4: Sin imágenes de apoyo
Cero imágenes ilustrativas (fotos de voluntarios, proyectos, eventos). Solo el logo y el icono decorativo de iconify. Una ONG gana credibilidad mostrando su comunidad.

---

## Positive Findings

1. **Uso correcto de `<article>` para proyectos** (línea 112) — semántica HTML5 que los lectores de pantalla interpretan correctamente.
2. **`scroll-mt-24` en el anchor** (línea 97) — demuestra conciencia del offset del navbar fijo.
3. **`v-if="estatutosUrl"`** (línea 84) — patrón defensivo que previene enlaces vacíos.
4. **Variantes responsive consistentes** — `text-4xl sm:text-5xl lg:text-6xl` en h1, `py-16 md:py-24` en secciones, `px-4 sm:px-6 lg:px-8` en contenedores. El responsive está sólido.
5. **Filosofía de componentes `Sections`/`Section`** — abstracción clara que permite mantener patrones consistentes y potencialmente añadir animaciones de entrada o lazy loading a nivel de sección.
6. **Importación limpia** — solo `ArrowRight` de `lucide-vue-next`, el resto viene de componentes globales o auto-importados. Sin dependencias innecesarias.
