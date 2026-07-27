# Análisis Global Final — Sitio Web TSEYOR

> **Fecha:** 15 de julio de 2026
> **Metodología:** Impeccable Critique (heurísticas Nielsen) + Impeccable Audit (5 dimensiones técnicas)
> **Páginas analizadas:** Portada, Filosofía, Quiénes Somos, ONG, Cursos, Navegación + Layout
> **Propósito:** Evaluación integral sin modificación de código

---

## 1. Resumen de Puntuaciones

### Design Health (Critique) — Heurísticas Nielsen /40

| Página | Score | Rating |
|--------|:-----:|--------|
| **Portada** (`Portada3.vue`) | **21/40** | Acceptable |
| **Filosofía** (`Filosofia.vue`) | **22/40** | Acceptable |
| **Quiénes Somos** (`QuienesSomos/index.vue`) | — | — |
| **ONG** (`Ong/Index.vue`) | **22/40** | Acceptable |
| **Cursos** (`Cursos/Index.vue`) | **24/40** | Acceptable |
| **Navegación** (`navigation.js` + `AppLayout.vue`) | **24/40** | Acceptable |
| **PROMEDIO GENERAL** | **~22.6/40** | **Acceptable** |

### Audit Health — 5 Dimensiones Técnicas /20

| Página | Score | Rating |
|--------|:-----:|--------|
| **Portada** | **11/20** | Acceptable |
| **Filosofía** | **12/20** | Acceptable |
| **Quiénes Somos** | — | — |
| **ONG** | **12/20** | Acceptable |
| **Cursos** | **12/20** | Acceptable |
| **Navegación** | **12/20** | Acceptable |
| **PROMEDIO GENERAL** | **~11.8/20** | **Acceptable** |

> El sitio está en la franja "Acceptable" — funcional, visualmente coherente, pero con brechas significativas en accesibilidad, rendimiento percibido y personalidad de marca que impiden pasar a "Good".

---

## 2. Issues Críticos (P0) — Acción Inmediata

| # | Issue | Página | Categoría | Impacto |
|---|-------|--------|-----------|---------|
| 1 | **`computed` sin import en Vue 3** | Cursos | Bug runtime | `ReferenceError` potencial en strict mode |
| 2 | **Dead code `v-if="false"` en sección "Próximos cursos"** | Cursos | Funcionalidad | La página no muestra cursos a pesar de llamarse "Cursos"; admins publican contenido invisible |
| 3 | **URL matching bug `indexOf` con `url: "/"`** | Navegación | Bug latente | `_in()` en `nav.js:106` matchea TODAS las rutas con `/`, causando tabs activos inconsistentes |
| 4 | **Enlaces externos con `target="_self"`** | Navegación | UX | Muular Electrónico y TSEYOR Canva navegan en la misma pestaña, el usuario pierde contexto del sitio |

---

## 3. Issues Graves (P1) — Siguiente Sprint

### 3.1 UX / Diseño

| # | Issue | Página | Detalle |
|---|-------|--------|---------|
| 5 | **8/9 anti-patrones AI-gen en portada** | Portada | Glassmorphism, hero-metrics, eyebrow labels, gradient icons, card grids idénticos, border-radius excesivo — la portada parece SaaS startup, no comunidad espiritual |
| 6 | **Sidebar de filosofía invisible en móvil** | Filosofía | `lg:grid` oculta el índice sticky en <1024px; usuario sin navegación en página de ~470 líneas |
| 7 | **Falsa affordance en pills "ideas esenciales"** | Filosofía | `cursor-help` sin tooltip — el usuario hoverea esperando ayuda y no recibe nada |
| 8 | **Sin hash en URL de secciones de filosofía** | Filosofía | No se puede compartir enlace directo a `#naturaleza-humana` o `#sociedades-armonicas` |
| 9 | **CTA sin feedback post-click** | ONG, Portada | Botones "Donar ahora", "Quiero colaborar", "Hazte voluntario" sin loading state ni confirmación |
| 10 | **5 secciones de ONG con layout idéntico** | ONG | Mismo padding, mismo ancho, mismo patrón label→h2→p — fatiga visual sin picos de atención |
| 11 | **Contenido institucional hardcodeado en frontend** | ONG | `ambitos` y `proyectos` en arrays Vue, no desde CMS — editar requiere deploy |
| 12 | **Items deshabilitados sin explicación** | Navegación | Publicaciones y TSEYOR Canva aparecen como `disabled` sin tooltip ni badge — el usuario intenta clickear y no pasa nada |
| 13 | **Submenú de Blog comentado** | Navegación | Código muerto que parece decisión a medias |

### 3.2 Accesibilidad

| # | Issue | Páginas | Detalle |
|---|-------|---------|--------|
| 14 | **Contraste insuficiente en texto al 60-65%** | TODAS | `text-base-content/60` y `/65` usado extensivamente no alcanza WCAG AA 4.5:1 sobre bg claro |
| 15 | **Animaciones sin `prefers-reduced-motion`** | Portada | Waveform, marquee, pulse — sin alternativa para usuarios con sensibilidad vestibular |
| 16 | **Sin focus-visible personalizado** | TODAS | Los botones dependen del estilo por defecto del navegador |
| 17 | **Hero background sin lazy-load** | Portada | `background-image` inline bloquea el primer paint |

### 3.3 Mantenibilidad

| # | Issue | Página | Detalle |
|---|-------|--------|---------|
| 18 | **Props sin tipo** | Cursos | `proximosCursos` sin `type: Array` — si backend envía null, `map()` rompe |
| 19 | **Sección oculta con `hidden`** | Portada | "¿Por dónde empezar?" (línea 91) desactivada sin decisión |
| 20 | **Logo ONG en JPG raster** | ONG | `logo-ong-mundo-armonico.JPG` se pixelará en retina; debería ser SVG o WebP |
| 21 | **Rutas mezcladas: literales vs `route()`** | ONG, Cursos | Algunas rutas usan strings (`/donar`, `/centros`), otras `route()` — inconsistencia |

---

## 4. Patrones Sistémicos

### 4.1 AI Slop Transversal (Alta severidad)

Todas las páginas analizadas comparten patrones de diseño AI-gen:

| Patrón | Ocurrencias |
|--------|:-----------:|
| Tiny uppercase tracked eyebrow en cada sección | 7+ por página |
| Border-radius >= 16px en cards | 5-10 por página |
| Glassmorphism decorativo (backdrop-blur + bg-opacity) | 3-5 por página |
| Gradient icons idénticos (from-primary to-primary/70) | Recurrente |
| Estructura repetitiva sección → sección → sección | Transversal |

**Impacto**: El sitio comunica "espiritualidad única" pero visualmente dice "template genérico 2024". La identidad de marca TSEYOR (guías estelares, consciencia cósmica, Sociedades Armónicas) no se refleja en el sistema de diseño.

### 4.2 Sin Estados de Carga ni Error (Alta severidad)

Ninguna página maneja explícitamente:
- Estados de carga (skeleton/spinner)
- Estados vacíos (empty state con guidance)
- Estados de error (fallback UI, retry)
- Feedback post-acción (confirmación, toast)

**Impacto**: En conexiones lentas o errores de backend, el usuario ve pantallas en blanco o parcialmente renderizadas sin indicación de progreso.

### 4.3 Arquitectura de Información Desbordada (Media severidad)

- **50+ enlaces** en el sistema de navegación
- **7-8 tabs** principales (supera el límite de working memory 5±2)
- **3-4 niveles de profundidad** en algunos submenús
- **Redundancia de labeling**: "Biblioteca > Biblioteca > Biblioteca Tseyor"
- **Sin breadcrumbs** en ninguna página

**Impacto**: Un usuario nuevo no puede construir un modelo mental del sitio. El menú refleja la estructura orgánica de la organización, no las tareas del usuario.

### 4.4 Estructura de Componentes Consistente (Fortaleza)

Todas las páginas usan un patrón común:
- `<Sections>` + `<Section>` wrapper
- `Back` + `Share` en header
- `font-display font-black` para títulos
- `tracking-[0.3em]` para labels
- Tokens DaisyUI (`primary`, `base-content`, `base-100`)

**Impacto**: El sitio se siente cohesivo. Un cambio de tema DaisyUI se propaga correctamente. La base técnica es sólida.

---

## 5. Mapa de Calor por Página

| Página | Critique /40 | Audit /20 | AI Slop | Salud Código | Prioridad |
|--------|:-----------:|:---------:|:-------:|:------------:|:---------:|
| **Cursos** | 24 | 12 | 🔴 Medio | 🔴 2 bugs P0 | 🔴 ALTA |
| **Navegación** | 24 | 12 | 🔴 Alto | 🟡 Bug P0 latente | 🔴 ALTA |
| **Portada** | 21 | 11 | 🔴 Muy Alto (8/9) | 🟡 Sin error states | 🟡 MEDIA |
| **Filosofía** | 22 | 12 | 🟡 Medio-Alto | 🟢 Sólido | 🟡 MEDIA |
| **ONG** | 22 | 12 | 🟡 Medio | 🟡 Hardcode data | 🟡 MEDIA |
| **Quiénes Somos** | — | — | 🟡 Medio | 🟢 Sólido | 🟢 BAJA |

---

## 6. Recomendaciones Priorizadas

### Inmediato (P0)
1. **Cursos**: Importar `computed` desde Vue (`import { computed } from 'vue'`)
2. **Cursos**: Decidir suerte de sección "Próximos cursos" — activar o eliminar dead code
3. **Navegación**: Arreglar `indexOf` en `nav.js` — usar `url === tab.url` con excepción para `/`
4. **Navegación**: Cambiar `target="_self"` a `target="_blank"` + `rel="noopener"` en enlaces externos

### Primer Sprint (P1)
5. **Todas**: Revisar contraste de textos al 60-65% — subir a 80% mínimo en body text
6. **Portada**: Reducir señales AI-gen — eliminar glassmorphism decorativo, simplificar eyebrow labels, unificar border-radius
7. **Filosofía**: Implementar navegación móvil alternativa (select desplegable o bottom nav)
8. **Filosofía**: Añadir tooltips reales a pills de "ideas esenciales"
9. **Filosofía**: Manejar `window.location.hash` para compartir secciones por URL
10. **ONG**: Migrar datos de frontend (`ambitos`, `proyectos`) a props del backend
11. **ONGP**: Convertir logo JPG a SVG o WebP con srcset
12. **Navegación**: Añadir tooltips/badges a items deshabilitados o eliminarlos
13. **Navegación**: Restaurar o limpiar submenú de Blog
14. **Portada**: Mover hero background a `<img>` con `loading="lazy"` o preload

### Segundo Sprint (P2-P3)
15. **Todas**: Añadir `prefers-reduced-motion` a todas las animaciones
16. **Todas**: Implementar breadcrumbs en layout principal
17. **Todas**: Añadir estados de carga (skeleton) mientras Inertia navega
18. **Todas**: Añadir estados vacíos con guidance para listas y grids
19. **Portada**: Consolidar set de iconos (unificar Lucide vs SVG inline)
20. **Navegación**: Considerar consolidación de tabs (fusionar Curso + Filosofía, o mover items a footer)
21. **Navegación**: Limpiar redundancia de labeling (Biblioteca > Biblioteca Tseyor → Biblioteca Tseyor directo)

---

## 7. Fortalezas Identificadas

| Fortaleza | Páginas | Por qué funciona |
|-----------|---------|-----------------|
| **Sistema de diseño DaisyUI consistente** | TODAS | Tematización sólida, tokens reutilizados, cambio de tema sin roturas |
| **Jerarquía tipográfica cuidada** | TODAS | `font-display`, `font-black`, escala progresiva sm/md/lg — bien calibrada |
| **Responsive mobile-first** | TODAS | Breakpoints sm/md/lg usados consistentemente, layouts fluidos |
| **Reproductor de audio integrado** | Portada | Waveform + marquee + seek bar — detalle técnico notable |
| **Sticky sidebar con IntersectionObserver** | Filosofía | Navegación interna elegante con tracking activo |
| **Componentes reutilizables** | TODAS | `Sections`, `Section`, `TextImage`, `FeatureColumns`, `Back`, `Share` |
| **Hover inteligente en navbar** | Navegación | 120ms delay + hover-helper extiende hit area, evita cierres prematuros |
| **Underscore animado en tabs** | Navegación | Transición CSS fluida con priorización hover > current |
| **Escape key cierra submenús** | Navegación | Estándar de accesibilidad bien implementado |
| **FAQ con `<details>` nativo** | Varias | Accesible, funcional sin JS, consistente con DaisyUI |

---

## 8. Conclusión

El sitio web TSEYOR tiene una **base técnica sólida** (Laravel + Inertia + Vue 3 + DaisyUI bien integrados) y una **consistencia visual encomiable** (tipografía, colores, componentes reutilizados). Sin embargo, arrorta **tres problemas sistémicos** que impiden pasar de "Acceptable" a "Good":

1. **Identidad visual genérica**: La acumulación de 8+ patrones AI-gen en cada página diluye la personalidad única de TSEYOR. El contenido es espiritual y profundo, pero el diseño dice "SaaS template".

2. **Arquitectura de información desbordada**: 50+ enlaces en el menú, sin breadcrumbs, sin navegación interna en páginas largas. El usuario nuevo se enfrenta a una pared de opciones.

3. **Falta de robustez en estados**: Sin loading states, sin error handling, sin empty states. El sitio funciona en el happy path pero se rompe silenciosamente en el borde.

**Score global ponderado: ~23/40 critique + ~12/20 audit = en zona "Acceptable, con potencial para Good"**

---

## 9. Archivos de Análisis Individuales

| Archivo | Contenido |
|---------|-----------|
| `analisis/impeccable-critique-portada.md` | Critique UX de Portada3.vue |
| `analisis/impeccable-audit-portada.md` | Auditoría técnica de Portada3.vue |
| `analisis/impeccable-critique-filosofia.md` | Critique UX de Filosofia.vue |
| `analisis/impeccable-audit-filosofia.md` | Auditoría técnica de Filosofia.vue |
| `analisis/impeccable-critique-quienes-somos.md` | Critique UX de QuienesSomos |
| `analisis/impeccable-audit-quienes-somos.md` | Auditoría técnica de QuienesSomos |
| `analisis/impeccable-critique-ong.md` | Critique UX de Ong/Index.vue |
| `analisis/impeccable-audit-ong.md` | Auditoría técnica de Ong/Index.vue |
| `analisis/impeccable-critique-cursos.md` | Critique UX de Cursos/Index.vue |
| `analisis/impeccable-audit-cursos.md` | Auditoría técnica de Cursos/Index.vue |
| `analisis/impeccable-critique-navegacion.md` | Critique UX de navegación + layout |
| `analisis/impeccable-audit-navegacion.md` | Auditoría técnica de navegación + layout |
| `informe-estado-actual.md` | Estado actual del sitio antes del análisis |
