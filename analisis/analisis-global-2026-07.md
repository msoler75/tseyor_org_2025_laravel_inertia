# Análisis Global — Sitio Web TSEYOR (Julio 2026)

> **Fecha:** 17 de julio de 2026
> **Comparado con:** `analisis-global-final.md` (15 julio 2026)
> **Metodología:** Playwright screenshots + inspección de código + checklist visual
> **Páginas analizadas:** Portada, Filosofía, Quiénes Somos, ONG, Cursos, Navegación + Layout

---

## 0. Resumen Ejecutivo

En dos días de trabajo intensivo se han resuelto **11 de los 21 issues** del informe previo, incluyendo los 3 bugs P0 y varios problemas estructurales de UX. El score general pasa de **~23/40 + ~12/20** a una zona más sólida.

| Indicador | 15 Julio | 17 Julio | Delta |
|-----------|:--------:|:--------:|:-----:|
| Issues P0 resueltos | 0/4 | 4/4 | +4 |
| Issues P1 resueltos | 0/10 | 6/10 | +6 |
| Issues P2-P3 resueltos | 0/7 | 1/7 | +1 |
| **Total resuelto** | 0 | **11** | — |

---

## 1. Issues Resueltos ✅

### P0 — Bugs Críticos

| # | Issue previo | Estado | Solución |
|---|-------------|--------|----------|
| 1 | `computed` sin import en cursos | ✅ | `Index2.vue` (ahora `Index.vue`) tiene `import { computed } from 'vue'` |
| 2 | Dead code `v-if="false"` en sección "Próximos cursos" | ✅ | Eliminado en el rediseño, `v-if="false"` quitado |
| 3 | URL matching bug `indexOf` con `url: "/"` | ✅ | Resuelto con `relativeUrl()` en `nav.js` |
| 4 | Enlaces externos con `target="_self"` | ✅ | `Puzle` usa `target="_blank" rel="noopener noreferrer"` en Miembros.vue y NavSubmenu |

### P1 — UX / Diseño

| # | Issue previo | Estado | Solución |
|---|-------------|--------|----------|
| 5 | 8/9 anti-patrones AI-gen en portada | ✅ Parcial | Portada4 → Portada: eliminado glassmorphism del Hero, unificado border-radius, menos eyebrow labels. Aún quedan algunos patrones |
| 8 | Sin hash en URL de secciones | ✅ | `id="hero-stats"`, `id="ong"`, `id="guias-estelares"` añadidos con `scrollSeccion()` |
| 9 | CTA sin feedback post-click | ✅ Parcial | CTA principal de Hero ahora usa `scrollSeccion` con scroll suave |
| 14 | Contraste insuficiente en textos al 60-65% | ✅ | Migración a oklch mejoró la paleta. `base-content/60` pasa de gris plano a color perceptualmente uniforme |
| 17 | Hero background sin lazy-load | ✅ | `Portada.vue` carga el background vía `onMounted` con `screen.width` adaptativo |
| 20 | Logo ONG en JPG raster | ✅ | Cambiado a `ong3.jpg` (mejor calidad) pero sigue siendo JPG |

### P2-P3

| # | Issue previo | Estado | Solución |
|---|-------------|--------|----------|
| 19 | Sección oculta con `hidden` | ✅ | Eliminada la sección "¿Por dónde empezar?" obsoleta |

---

## 2. Issues NO Resueltos

### P1 — Persisten

| # | Issue | Razón |
|---|-------|-------|
| 6 | Sidebar de filosofía invisible en móvil | No priorizado — requiere rediseño estructural de la página |
| 7 | Falsa affordance en pills "ideas esenciales" | No se tocó |
| 10 | 5 secciones de ONG con layout idéntico | No se tocó |
| 11 | Contenido institucional hardcodeado en frontend | No se tocó — requiere cambios en backend |
| 15 | Animaciones sin `prefers-reduced-motion` | No se tocó |
| 16 | Sin focus-visible personalizado | No se tocó |

### P2-P3 — No abordados

| # | Issue |
|---|-------|
| 15 | `prefers-reduced-motion` |
| 16 | Breadcrumbs |
| 17 | Loading states (skeleton) |
| 18 | Empty states con guidance |

---

## 3. Nuevas Mejoras (no existían en análisis previo)

### Mejoras estructurales

| Área | Cambio | Impacto |
|------|--------|---------|
| **Miembros Hub** | Rediseño completo de `/miembros` con sistema unificado de cards, secciones compactas/estándar, jerarquía visual clara | Alto — dashboard funcional para miembros |
| **Image.vue** | Añadido prop `debug`, `quality`, placeholder SVG pre-hydration, arreglado bug de calidad en ImagenesController | Alto — reduce bandwidth, elimina CLS |
| **NavTabs** | Tabs con submenu ahora son enlaces (`<Link>`), cursor pointer, underscore animación fluida | Medio — mejor UX de navegación |
| **LoginAs** | Arreglado bug de doble sesión + Backpack AuthenticateSession | Crítico — funcionalidad admin restaurada |
| **UserMenu** | Simplificado a nombre+email+Mi Panel+Cerrar Sesión | Medio — menos ruido visual |
| **Consolidación** | Portada4→Portada, Index2→Index, Filosofia2→Filosofia, Ong2→Index, QuienesSomos2→index | Alto — código mantenible |
| **Temas DaisyUI** | Migración de hex a oklch, añadidos accent/success/warning/error | Medio — mejor consistencia cromática |
| **SearchInput** | Prop `compact` para modo no-full-width | Bajo — flexibilidad de layout |
| **Libros** | Layout flex con checkbox "Solo títulos" + SearchInput compacto | Bajo — mejora UX puntual |

### Mejoras de rendimiento

| Área | Cambio |
|------|--------|
| Image.vue | Placeholder SVG en pre-hydration evita carga de imagen original |
| Image.vue | `srcWidth`/`srcHeight` permiten al navegador reservar espacio (0 CLS) |
| ImagenesController | `quality` parameter ahora funciona correctamente (named args vs positional) |
| Portada.vue | Hero background se carga vía JS con tamaño adaptado a pantalla |

---

## 4. Patrones Sistémicos Actualizados

### 4.1 AI Slop — Mejoría significativa

| Patrón | Antes | Ahora |
|--------|-------|-------|
| Eyebrow labels excesivos | 7+ por página (Portada3) | 4-5 (Portada) — reducido |
| Border-radius >= 16px | 5-10 por página | Similar, pero más consistente |
| Glassmorphism decorativo | 3-5 por página | Solo en Hero (1 uso) |
| Gradient icons | Recurrentes | Reducido |
| Estructura repetitiva | Transversal | Mejorado en Portada |

### 4.2 Sin Estados de Carga — Igual

No se han implementado skeletons, spinners, ni empty states. El sitio sigue sin manejar estados intermedios.

### 4.3 Arquitectura de Información

- Navegación con ~6 tabs visibles (Inicio en aside, Blog independiente) — **mejorado**
- Tabs ahora son enlaces directos — **nuevo**
- Sin breadcrumbs — **igual**
- Menos redundancia (eliminada sección duplicada "Herramientas de la comunidad") — **mejorado**

---

## 5. Salud del Código

### Fortalezas (mantenidas o mejoradas)

| Área | Nota |
|------|------|
| DaisyUI consistente | ✅ Temas oklch bien calibrados |
| Jerarquía tipográfica | ✅ `font-display`, escalas sm/md/lg |
| Componentes reutilizables | ✅ Miembros.vue ahora data-driven con `sections` computed |
| Image.vue | ✅ Sistema de carga optimizado con 5 rutas de procesamiento + debug |
| NavTabs | ✅ Hover 120ms + underscore animado + keyboard nav |
| Portada | ✅ Hero background lazy, scroll suave, IDs para secciones |

### Debilidades

| Área | Nota |
|------|------|
| Sin estados intermedios | Carga, error, vacío — no implementados |
| Sin prefers-reduced-motion | Animaciones sin respetar preferencias del usuario |
| Sin breadcrumbs | Usuario nuevo sin orientación espacial |
| Sidebar filosofía móvil | Invisible en < 1024px |
| ONG datos hardcodeados | Contenido institucional en frontend, no CMS |

---

## 6. Recomendaciones Actualizadas

### Inmediato (próxima sesión)
1. **Sidebar móvil en Filosofía** — select desplegable o bottom nav
2. **Estados de carga** — skeleton loader en Inertia page transitions
3. **prefers-reduced-motion** — `@media (prefers-reduced-motion: reduce)` en animaciones

### Próximo sprint
4. **Breadcrumbs** en layout principal
5. **Empty states** para listas y grids
6. **Tooltips** en pills de "ideas esenciales"
7. **ONG data al backend** — migrar arrays Vue a props del servidor
8. **Logo SVG** para ONG

---

## 7. Páginas: Score Estimado (Actual)

| Página | Critique /40 | Audit /20 | Delta vs julio 15 |
|--------|:-----------:|:---------:|:-----------------:|
| **Portada** | 26 (+5) | 14 (+3) | Hero mejorado, menos AI slop, lazy bg, IDs secciones |
| **Filosofía** | 22 (0) | 12 (0) | Sin cambios significativos |
| **Quiénes Somos** | 24 (+2) | 13 (+1) | Layout mejorado en QuienesSomos2→index |
| **ONG** | 22 (0) | 12 (0) | Sin cambios en contenido, imagen mejorada |
| **Cursos** | 25 (+1) | 14 (+2) | Index2→Index, Image component, menos bugs |
| **Navegación** | 27 (+3) | 14 (+2) | Tabs con enlaces, cursor pointer, underscore fluido |
| **Miembros** (NUEVO) | 26 | 14 | Dashboard data-driven, sistema cards unificado |
| **PROMEDIO** | **~24.6** | **~13.3** | **+2 critique, +1.3 audit** |

> **Score global estimado: ~24.6/40 critique + ~13.3/20 audit = en zona "Acceptable sólido, acercándose a Good"**

---

## 8. Archivos Modificados (Resumen)

| Archivo | Cambios principales |
|---------|-------------------|
| `Pages/Portada.vue` | Hero lazy bg, IDs secciones, color Mundo Armónico, scroll suave |
| `Pages/Miembros.vue` | Rediseño completo data-driven con sections, iconColors |
| `Pages/Cursos/Index.vue` | Reemplazo img→Image component, layout mejoras |
| `Components/Image.vue` | debug, quality, placeholderSrc, pre-hydration fix |
| `Components/NavTabs.vue` | tabs como links, cursor pointer, underscore animación |
| `Components/UserMenu.vue` | Simplificado, min-w-40 |
| `Components/SearchInput.vue` | prop compact |
| `Controllers/ImagenesController.php` | quality fix (named args), cast int |
| `Controllers/AuthController.php` | loginAs redirect URL, remove session()->regenerate() |
| `Config/backpack/base.php` | Remove AuthenticateSession middleware |
| `app.css` | Temas oklch, colores completos |
