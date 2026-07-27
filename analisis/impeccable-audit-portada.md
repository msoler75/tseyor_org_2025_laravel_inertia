# Auditoría UX/UI — Portada3.vue (Portada TSEYOR)

## Audit Health Score — 5 Dimensiones

| # | Dimensión | Puntuación (0-4) | Resumen |
|---|-----------|:---:|---|
| 1 | **Accessibility (a11y)** | 2 | Uso correcto de `<details>` nativo y `<button>`, pero faltan estados de foco visibles, contraste insuficiente en textos al 60%, y animaciones sin `prefers-reduced-motion`. |
| 2 | **Performance** | 2 | Imágenes con `loading="lazy"` bien, pero hero background inline bloquea lazy-loading. Múltiples backdrop-filter, 24 spans animados en waveform, y transiciones CSS en hover que pueden causar repaints costosos. |
| 3 | **Responsive Design** | 3 | Mobile-first con breakpoints sm/md/lg consistentes. Sección meditación en móvil es riesgosa (min-h + max-h). Grid de stats funciona bien en todos los tamaños. |
| 4 | **Theming** | 3 | Uso consistente de tokens DaisyUI. Un color hardcodeado (`emerald-400` en línea 265) y un gradient hardcodeado (`#4f46e5` en línea 164) rompen la tematización. |
| 5 | **Anti-Patterns** | 1 | 8/9 señales de patrón AI-generated template. La acumulación es un riesgo de marca. |

**Total: 11 / 20**

---

## Anti-Patterns Verdict

**Severidad: ALTA** — 8 de 9 señales AI-gen detectadas.

| # | Anti-Patrón | Líneas | Impacto |
|---|------------|--------|---------|
| 1 | **Eyebrow labels en todas las secciones** | 11, 126, 167, 346, 387, 422, 502 | Saturan visualmente; cada sección grita su categoría en vez de fluir naturalmente |
| 2 | **Glassmorphism decorativo** | 7, 50, 260 | `backdrop-blur-xs/lg/xl` sin función clara; la meditación card apila 2 capas de blur |
| 3 | **Hero-metrics template** | 48-87 | Stats grid pegado al hero, patrón SaaS genérico para una ONG |
| 4 | **Gradient icons idénticos** | 51, 60, 68, 77 | 4 iconos con exacto `bg-gradient-to-br from-primary to-primary/70` |
| 5 | **Card grid idéntico** | 354-371 | 4 cards de filosofía con estructura clonada: imagen → título → descripción → "Saber más" |
| 6 | **Border-radius excesivo** | múltiples | 16px en cards, 24px en contenedores, 32px en hero card — desgastan espacio útil |
| 7 | **Side-stripe border** | 138 | `border-l-4 border-primary` en blockquote |
| 8 | **Decorative grid** | 164 | `radial-gradient(#4f46e5...)` — patrón genérico de templates |

---

## Executive Summary

**Score global: 11/20 (Regular)**

Portada3.vue es visualmente pulida pero funcionalmente frágil. El mayor problema es la **identidad de marca diluida por 8 anti-patrones AI-gen**: parece una landing de startup tech, no la puerta de entrada a una comunidad espiritual. En accesibilidad, el contraste de textos secundarios y la falta de control de animaciones son los riesgos principales. En performance, el hero background es el cuello de botella más obvio.

---

## Detailed Findings by Severity

### P0 — Crítico (0 hallazgos)

---

### P1 — Alto (3 hallazgos)

**P1.1 — Múltiples animaciones sin `prefers-reduced-motion`**
- **Ubicación**: Portada3.vue, líneas 265 (pulse), 291 (waveform), 300 (marquee), 676-678 (clases CSS)
- **Categoría**: Accesibilidad / Motion Sensitivity
- **Impacto**: Usuarios con trastornos vestibulares o sensibilidad al movimiento (migraña, epilepsia) pueden experimentar malestar o no poder usar la página.
- **Recomendación**: Envolver keyframes en `@media (prefers-reduced-motion: no-preference)` o usar la utilidad de DaisyUI/Tailwind `motion-reduce:*`. Ejemplo:
  ```css
  @media (prefers-reduced-motion: no-preference) {
    .animate-waveform { animation: waveform ... }
  }
  ```
  Solo Lenis lo respeta (línea 556). El CSS scoped no.

**P1.2 — Hero background image no lazy-loadable y sin fallback**
- **Ubicación**: Portada3.vue, línea 4
- **Categoría**: Performance
- **Impacto**: Imagen de ~500-800KB (estimado) cargada inline en style, bloquea el primer paint y no permite lazy-loading nativo. Sin fallback si la URL falla.
- **Recomendación**: Usar `<img>` con `loading="lazy"` dentro del hero con `object-cover`, o precargar con `<link rel="preload">` para controlar la carga. Añadir un color de fondo como fallback.

**P1.3 — Sección oculta con dead code (línea 91)**
- **Ubicación**: Portada3.vue, líneas 90-109
- **Categoría**: Mantenibilidad / UX
- **Impacto**: Código muerto que aumenta el bundle sin aportar valor. La sección "¿Por dónde empezar?" sería útil para first-timers pero está desactivada sin decisión tomada.
- **Recomendación**: Decidir: (a) eliminarla y reducir bundle, o (b) activarla condicionalmente para usuarios primerizos detectados por sesión.

---

### P2 — Medio (4 hallazgos)

**P2.1 — Texto con contraste insuficiente**
- **Ubicación**: Portada3.vue, múltiples líneas — `text-base-content/60` (ej: líneas 19, 134, 235, 393, 429), `text-base-content/40` (líneas 56, 74, 83, 316), `text-primary/80` (línea 25)
- **Categoría**: Accesibilidad / Contraste
- **Impacto**: `text-base-content/60` sobre `bg-base-100` puede no alcanzar WCAG AA (ratio 4.5:1). Depende del color base definido en el tema, pero es un riesgo real para textos de 14-16px.
- **Recomendación**: Limitar opacidad a `/80` como mínimo para textos de lectura. Usar `/60` solo para textos decorativos o metadata. Verificar con herramienta de contraste.

**P2.2 — Sin placeholder ni estado vacío para grids dinámicos**
- **Ubicación**: Portada3.vue, líneas 354 (filosofía grid), 399 (entradas grid)
- **Categoría**: UX / Error Prevention
- **Impacto**: Si `paginasFilosofia` o `entradasRecientes` llegan vacíos, se renderiza un grid vacío con solo bordes y un enlace "Explorar" sin contenido. Experiencia confusa.
- **Recomendación**: Añadir `v-if="paginasFilosofia?.length"` o un mensaje tipo "Próximamente..." con un skeleton loader.

**P2.3 — Inconsistencia en sets de iconos**
- **Ubicación**: Portada3.vue, línea 52 (SVG inline de reloj en stats)
- **Categoría**: Consistencia
- **Impacto**: La mayoría de iconos usan `lucide-vue-next`. El SVG inline del reloj (línea 52) y otros en stats son raw SVG, lo que rompe la consistencia visual (stroke-width, diseño) y dificulta mantenimiento.
- **Recomendación**: Reemplazar SVGs inline por sus equivalentes de Lucide (`Clock`, `Book`, `MessageSquare`, `Shield`).

**P2.4 — Reproductor de audio sin control de volumen ni estado de error**
- **Ubicación**: Portada3.vue, líneas 277-320 (script líneas 616-638)
- **Categoría**: UX / Functional Completeness
- **Impacto**: El usuario no puede ajustar volumen. Si el audio falla (URL rota, red caída), no hay feedback — el botón muestra "Escuchando" pero no hay sonido.
- **Recomendación**: Añadir control de volumen al player y un estado de error con retry. La store `usePlayer` (línea 529) debería exponer un estado de error.

---

### P3 — Bajo (3 hallazgos)

**P3.1 — `toggleMeditacion` sin guard contra null**
- **Ubicación**: Portada3.vue, línea 624-631
- **Categoría**: Robustez
- **Impacto**: Si `meditacionActual` es null, `meditacionActual.titulo` (línea 629) fallaría. La función retorna temprano si no hay URL (línea 625), pero la llamada a `player.play()` con título null es insegura.
- **Recomendación**: Añadir `if (!meditacionActual.value) return` al inicio de la función.

**P3.2 — Color hardcodeado emerald-400**
- **Ubicación**: Portada3.vue, línea 265
- **Categoría**: Theming
- **Impacto**: `bg-emerald-400` y `text-emerald-400` rompen la tematización. Si el tema cambia (dark mode, colores alternativos), este indicador verde de "en vivo" no se adapta.
- **Recomendación**: Usar `bg-success` y `text-success` de DaisyUI, o el token de color correspondiente.

**P3.3 — Gradient hardcodeado en decorative grid**
- **Ubicación**: Portada3.vue, línea 164
- **Categoría**: Theming
- **Impacto**: `radial-gradient(#4f46e504_1px,transparent_1px)` usa un color fijo (#4f46e5, indigo-600) que no es un token de DaisyUI. No se adapta al tema.
- **Recomendación**: Usar una variable CSS o token DaisyUI: `radial-gradient(color-mix(in srgb, var(--p) 2%, transparent) 1px, transparent 1px)`.

---

## Patterns & Systemic Issues

1. **Patrón: Eyebrow como muleta estructural** — Cada sección depende de un badge `uppercase tracking-widest` para explicar de qué trata. Esto sugiere que los títulos y el contenido no comunican el tema por sí mismos. Es un patrón de diseño perezoso.

2. **Patrón: Decoración acumulativa** — Cada sección apila: (1) eyebrow badge, (2) gradient/blur decorative element, (3) border-radius grande, (4) hover transitions. Individualmente funcionan, pero juntos crean fatiga visual y un estilo genérico.

3. **Patrón sistémico: Sin distinción entre contenido primario y secundario** — El hero compite con stats, filosofía, comunidad, FAQ, CTA final — todos con pesos visuales similares. No hay jerarquía de importancia más allá del orden vertical.

4. **Ausencia de sistema de carga/error** — No hay componentes de estado (loading, empty, error) en ningún lugar del template. Es un riesgo arquitectónico: el template asume que los datos siempre llegarán correctamente.

---

## Positive Findings

1. **FAQ con `<details>` semántico y accesible** — Bien implementado, con el CSS `[&_summary::-webkit-details-marker]:hidden` para personalizar la flecha sin perder funcionalidad nativa. Las respuestas están en lenguaje claro y directo.

2. **Uso correcto de lazy-loading en imágenes** — `loading="lazy"` en todas las imágenes del template (libros, comunidad, guías, entradas). Esto mejora significativamente el LCP y la experiencia en conexiones lentas.

3. **Carga diferida de Lenis** — `await import('lenis')` (línea 560) es una decisión técnica correcta: la librería de smooth scroll se carga solo si el navegador no tiene `prefers-reduced-motion`, y no bloquea el render inicial.

4. **Coherencia tipográfica** — `font-display` aplicado consistentemente a títulos y `font-light` a body text. La jerarquía tipográfica es clara y profesional.

5. **Transiciones bien implementadas** — Las animaciones de hover (`hover:scale-105`, `hover:-translate-y-0.5`) son sutiles y performantes (solo transform/opacity, que no disparan layout).
