# Auditoría UX/UI — Filosofía TSEYOR (`Filosofia.vue`)

---

## Audit Health Score

| # | Dimensión | Puntaje | Observación breve |
|---|-----------|---------|-------------------|
| 1 | **Accessibility (a11y)** | 2/4 | Estructura semántica correcta (article, nav, h1/h2), pero faltan aria-current, tooltips en pills, contraste bajo en textos secundarios, y no hay skip link. |
| 2 | **Performance** | 3/4 | Componente ligero: solo texto + icons con IntersectionObserver. Sin imágenes pesadas ni animaciones complejas. Potencial problema: Observer + scroll handler sin debounce si se añaden más secciones. |
| 3 | **Responsive Design** | 2/4 | Layout fluido con breakpoints sm/md/lg funcionando, pero la pérdida del sidebar en móvil es crítica y los border-radius excesivos se ven desproporcionados en pantallas pequeñas. |
| 4 | **Theming** | 3/4 | Uso correcto de variables DaisyUI (base-content, primary, base-100/200/300). No hay hardcodeo de colores excepto en gradients. El tracking funciona con cualquier tema. |
| 5 | **Anti-Patterns** | 2/4 | Ver sección detallada abajo. 5 de 9 anti-patrones AI identificados. Repetición estructural severa entre secciones. |

**Total: 12/20** — Puntaje aceptable pero con brechas notables en accesibilidad y responsive.

---

## Anti-Patterns Verdict

| Anti-Pattern | Severidad | Evidencia concreta |
|---|---|---|
| Tiny uppercase tracked eyebrow | P2 | 7 ocurrencias idénticas: `text-xs font-bold uppercase tracking-[0.3em] text-primary/60 mb-6 font-display` (líneas 13, 53, 82, 133, 198, 248, 310) |
| Card grids idénticos | P2 | 4 cards en `camino` array (líneas 210-216) con estructura 100% idéntica: icon container → h3 → p |
| Numbered section markers | P1 | Sidebar números del 1 al 4 (líneas 63-68). Útil para navegación pero es un marcador clásico de plantilla. |
| border-radius excesivo | P2 | `rounded-3xl` (24px) en cards de contenido (línea 29, 115, 180, 219, 283, 290, 318). `rounded-2xl` (16px) en icon containers. |
| Glassmorphism decorativo | P3 | `bg-base-100/60 border border-base-300/30` en múltiples callouts (línea 29). No es agresivo pero aparece en cada sección. |

---

## Executive Summary

**Filosofia.vue** es una página de contenido extenso con una base estructural sólida y un diseño visual atractivo, pero arrastra problemas de accesibilidad (contraste, affordances falsas) y responsive (sidebar desaparece en móvil). El patrón repetitivo entre las 4 secciones narrativas —aunque consistente y predecible— cruza la línea hacia la monotonía estructural.

Puntaje total: **12/20**. Las principales oportunidades de mejora están en a11y (2/4) y responsive (2/4).

**Top 3 issues por impacto**:
1. **P0**: Sin acceso al índice de navegación en móvil — pérdida de orientación total
2. **P1**: Falsa affordance en pills "ideas esenciales" con `cursor-help` pero sin tooltip
3. **P1**: Sin manejo de URL hash — no se puede compartir secciones específicas

---

## Detailed Findings by Severity

### P0 — Severidad Crítica

No se detectaron issues P0. La página es funcional y no tiene breaks que impidan el uso completo.

---

### P1 — Severidad Alta

| Item | Detalle |
|------|---------|
| **Sidebar oculto en móvil** | `grid lg:grid-cols-[220px_1fr]` (línea 50) — el nav lateral SOLO existe en breakpoint lg+. En móvil/tablet no hay ningún control de navegación entre secciones. El usuario debe scrollear manualmente. |
| *Categoría* | Responsive Design |
| *Impacto* | 100% de usuarios móviles pierden la navegación. Dependiendo del tráfico móvil, puede ser el grupo mayoritario. |
| *Recomendación* | Implementar menú desplegable tipo `<select>` o bottom sheet con las 4 secciones en breakpoints < lg. |

| Item | Detalle |
|------|---------|
| **Falsa affordance en pills "ideas esenciales"** | `<Referencia class="cursor-help">` (líneas 120, 185, 236, 295) sin tooltip, popover, ni `title` attribute. El cursor cambia a help, pero no hay respuesta al hover/click. |
| *Categoría* | Accessibility / UX |
| *Impacto* | Usuarios que intentan interactuar reciben feedback CERO. Sensación de componente roto o placeholder. |
| *Recomendación* | Implementar DaisyUI Tooltip o al menos un `title` attribute con la definición breve de cada concepto. |

| Item | Detalle |
|------|---------|
| **Sin manejo de hash en URL** | Los anchors `#mensaje-cosmico` etc. funcionan para scroll local, pero `window.location.hash` no se actualiza. Si alguien comparte `url#naturaleza-humana`, el observer parte desde el primer artículo. |
| *Categoría* | UX / Shareability |
| *Impacto* | Imposible compartir enlace directo a una sección. Perjuicio para redes sociales y referencias. |
| *Recomendación* | En `onMounted`, leer y hacer scroll al hash si existe; agregar `history.replaceState` o `scrollIntoView` en el IntersectionObserver callback. |

| Item | Detalle |
|------|---------|
| **Contraste en textos al 60-65% de opacidad** | `text-base-content/65` y `/60` (líneas 89, 204, 215, 255, etc.) para párrafos completos. Con base-100 de fondo claro, el contraste efectivo cae ~3.5:1. |
| *Categoría* | Accessibility |
| *Impacto* | Fracasa en WCAG AA (4.5:1 para texto normal). Afecta a usuarios con baja visión, fatiga visual, o en exteriores. |
| *Recomendación* | Subir a `text-base-content/80` como mínimo para texto de párrafo, reservando `/60` solo para metadata o placeholders. |

---

### P2 — Severidad Media

| Item | Detalle |
|------|---------|
| **Patrón estructural repetitivo x4** | icon-gradient → eyebrow tracking → h2 → párrafos → callout → pills (secciones en líneas 78, 129, 194, 244). |
| *Categoría* | Anti-Patterns / Visual Design |
| *Impacto* | El usuario percibe monotonía visual. No hay diferenciación entre secciones más allá del contenido textual. La página se siente más larga de lo que es. |
| *Recomendación* | Variar el layout por sección: una con imagen, otra con timeline, otra con cards alternados. Romper el molde en al menos 2 de 4 bloques. |

| Item | Detalle |
|------|---------|
| **border-radius excesivo** | `rounded-3xl` (24px) en cards de contenido (líneas 29, 115, 180, 219, etc.). El estándar DaisyUI para cards es `rounded-box` (12px). |
| *Categoría* | Visual Design |
| *Impacto* | En móvil, un card de 24px de radio en pantalla de 375px de ancho se come ~6.4% del ancho disponible con pura curva. Visualmente ruidoso. |
| *Recomendación* | Usar `rounded-2xl` (16px) como máximo en desktop y `rounded-xl` (12px) en móvil. |

| Item | Detalle |
|------|---------|
| **Hero sin elemento visual** | Líneas 10-46: solo texto + un callout. Sin imagen, ilustración, pattern o gradiente de fondo completo. |
| *Categoría* | Visual Design / Branding |
| *Impacto* | Primer impacto visual pobre. Una página espiritual sin imagen en el hero envía el mensaje equivocado. |
| *Recomendación* | Añadir un fondo con gradiente o pattern SVG sutil, o una imagen abstracta de baja opacidad. |

| Item | Detalle |
|------|---------|
| **IntersectionObserver sin polyfill ni fallback** | Línea 369: `new IntersectionObserver(...)`. No hay `import 'intersection-observer'` ni verificación de soporte. |
| *Categoría* | Performance / Browser Support |
| *Impacto* | Navegadores antiguos (IE11, Safari <12.1) no soportan IO. Sin fallback, `activeSection` permanece en el valor inicial y el sidebar no trackea. |
| *Recomendación* | Añadir lazy load del polyfill condicional o un fallback con `scroll` event listener. |

---

### P3 — Severidad Baja

| Item | Detalle |
|------|---------|
| **Sin aria-current en nav activo** | Línea 56-72: el nav item activo cambia clase visual pero no hay `aria-current="section"`. |
| *Categoría* | Accessibility |
| *Impacto* | Lectores de pantalla no pueden identificar qué sección está activa. |
| *Recomendación* | Añadir `:aria-current="activeSection === clave.ancla ? 'section' : undefined"`. |

| Item | Detalle |
|------|---------|
| **No hay `text-balance` en headings** | Líneas 85-86, 136-137, etc.: headings largos que pueden quedar con viudas (1-2 palabras en última línea). |
| *Categoría* | Typography |
| *Impacto* | Viudas antiestéticas en headings, especialmente "humano y cósmico" que es corto. |
| *Recomendación* | Añadir `text-balance` con la utilidad de CSS text-wrap. |

| Item | Detalle |
|------|---------|
| **Gradiente imperceptible en callout** | Línea 166: `bg-gradient-to-br from-primary/10 to-base-100`. El fade a base-100 es casi invisible porque base-100 ya es el fondo. |
| *Categoría* | Visual Design |
| *Impacto* | El gradiente no aporta valor visual. Ocupa bytes sin beneficio. |
| *Recomendación* | Usar un gradiente más contrastado (`from-primary/15 to-primary/5`) o eliminarlo. |

| Item | Detalle |
|------|---------|
| **Touch targets pequeños en móvil** | Línea 120: `px-3 py-1.5` en pills → ~24px de altura. El mínimo touch target recomendado es 44px (Apple HIG, Material Design). |
| *Categoría* | Accessibility / Mobile |
| *Impacto* | Dedos grandes no pueden tocar con precisión. |
| *Recomendación* | Añadir `min-h-[44px]` con padding vertical suficiente en pills. |

---

## Patterns & Systemic Issues

1. **Patrón de repetición estructural**: El mismo layout de sección se replica 4 veces. Esto sugiere que el componente podría refactorizarse a un sub-componente `FilosofiaSection.vue` que reciba props (icono, eyebrow, título, contenido, ideas, tipo de callout). DRY aplicado a medias.

2. **Falta de progresión visual**: Las 4 secciones tienen el MISMO peso visual (mismo tamaño de icono, mismo tamaño de heading, mismo callout, mismas pills). No hay clímax, no hay sensación de avance. La sección 4 ("Sociedades Armónicas") debería sentirse como destino final, no como una repetición de la sección 1.

3. **Ausencia de sistema de navegación global**: La página no informa al usuario cómo encaja dentro del sitio TSEYOR. El componente `Back` va a `/quienes-somos` pero no hay breadcrumbs ni indicación de la jerarquía del sitio.

4. **Dependencia excesiva de un solo hook**: IntersectionObserver es la ÚNICA fuente de "estado de lectura". Si falla (JS, polyfill, o el usuario usa teclado para navegar), el sidebar queda inerte. No hay redundancia.

---

## Positive Findings

1. **IntersectionObserver bien configurado**: `rootMargin: '-80px 0px -60% 0px'` (línea 376) está calibrado para activar la sección cuando el usuario ha visto el 40% del bloque. Buena práctica.

2. **Contenido bien estructurado semánticamente**: `<article>` por sección, `<nav>` con `aria-label`, h1 → h2 jerarquía correcta, `<span>` para eyebrow (no falso heading). Esto es sólido para SEO y lectores de pantalla.

3. **Uso correcto de DaisyUI variables**: No hay colores hardcodeados (excepto transparentes y gradients). El componente se adapta correctamente a cualquier tema claro/oscuro. `base-content`, `primary`, `base-100/200/300` están usados consistentemente.

4. **Scroll-margin-top en artículos**: `scroll-mt-24` (líneas 78, 129, 194, 244) evita que el sticky header tape el inicio de la sección. Detalle que marca diferencia.

5. **Gap en grid responsive**: `gap-12 lg:gap-20` (línea 50) da espacio adecuado entre sidebar y contenido en desktop sin apretar en móvil.
