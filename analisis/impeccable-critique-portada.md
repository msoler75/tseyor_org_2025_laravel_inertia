# Crítica UX/UI — Portada3.vue (Portada TSEYOR)

## Design Health Score — 10 Heurísticas de Nielsen

| # | Heurística | Puntuación (0-4) | Hallazgos |
|---|-----------|:---:|---|
| 1 | **Visibility of System Status** | 2 | El reproductor de audio muestra progreso, waveform y tiempo — correcto. Pero: no hay estado de carga para la imagen de hero (background-image inline), no hay indicador de scroll progress, no hay feedback cuando las stats aún no se han cargado. |
| 2 | **Match System / Real World** | 3 | Lenguaje natural y claro ("ONG", "Biblioteca", "Eventos"). La sección FAQ está bien redactada. Sin embargo: "Confederación de Mundos Habitados de la Galaxia" como eyebrow de sección es jargon muy denso para un first-timer. |
| 3 | **User Control and Freedom** | 2 | `scrollSeccion()` permite navegación suave entre secciones. Pero no hay botón "volver arriba", el reproductor carece de control de volumen visible, y no hay forma de salir fácilmente de una sección a otra sin scroll manual. |
| 4 | **Consistency and Standards** | 3 | Botones consistentes: todos `rounded-full`, `uppercase`, `tracking-widest`, `text-xs`. Sin embargo, sección "¿Por dónde empezar?" (línea 91) está oculta con `hidden` — dead code que sugiere trabajo incompleto. Los iconos de Lucide se mezclan con SVGs inline sin un criterio claro. |
| 5 | **Error Prevention** | 2 | No hay validación visible. `toggleMeditacion()` (línea 624) no comprueba si `meditacionActual` es null antes de acceder a `.titulo`. Las imágenes sin `onerror` pueden romper el layout. La función `formatearNumero` (línea 611) es segura, pero no hay guard contra props vacías. |
| 6 | **Recognition Rather Than Recall** | 3 | Combinaciones icono+texto en stats y enlaces ayudan al reconocimiento. FAQ usa `<details>` que permite expandir/colapsar. Pero la página es muy extensa (10 secciones) sin navegación sticky ni indicador de progreso — el usuario debe recordar dónde estaba. |
| 7 | **Flexibility and Efficiency** | 1 | Sin atajos de teclado, sin búsqueda, sin skip-to-section, sin personalización. Usuarios avanzados no tienen aceleradores. El scroll suave de Lenis es el único "plus" pero no es configurable por el usuario. |
| 8 | **Aesthetic and Minimalist Design** | 3 | Visualmente atractivo: buena jerarquía tipográfica, espaciado generoso, paleta de colores coherente. Pero: cada sección tiene eyebrow label, decoraciones superpuestas (backdrop-blur + gradients + decorative grid + border gradients), y los stats tienen 4 iconos con gradient casi idénticos. La densidad decorativa es alta. |
| 9 | **Error Recovery** | 1 | Sin estados de error visibles. Si falla una imagen → no hay placeholder. Si falla el audio → no hay mensaje de error. Lenis tiene un catch (línea 568) que es correcto, pero no informa al usuario. No hay retry mechanism para ninguna operación. |
| 10 | **Help and Documentation** | 1 | FAQ existe pero solo 4 preguntas. No hay ayuda contextual, no hay onboarding, no hay "cómo usar este sitio". El usuario novel debe explorar por su cuenta sin guía. |

**Total: 21 / 40**

---

## Anti-Patterns Verdict

Esta portada presenta **8 de 9 señales** del patrón AI-generated template:

| Señal | Presente | Evidencia |
|-------|:--------:|-----------|
| Gradient text/icons | ✅ | 4 iconos de stats con `bg-gradient-to-br from-primary to-primary/70` (líneas 51, 60, 68, 77) |
| Glassmorphism decorativo | ✅ | Hero card (`backdrop-blur-xs`, línea 7), stats cards (`backdrop-blur-lg`, línea 50), meditación card (`backdrop-blur-xl`, línea 260) |
| Hero-metrics template | ✅ | Stats grid anclado al hero (líneas 48-87) — patrón típico de templates SaaS |
| Card grids idénticos | ✅ | 4 cards de filosofía (líneas 354-371) con estructura exactamente igual: imagen → título → descripción → "Saber más" |
| Tiny uppercase tracked eyebrow | ✅ | En **TODAS** las secciones: líneas 11, 126, 167, 346, 387, 422, 502 |
| Border-radius excesivo (>16px) | ✅ | `rounded-2xl` (16px) 3 veces, `rounded-3xl` (24px) 9 veces, `rounded-4xl` (32px) 1 vez |
| Side-stripe borders | ✅ | Blockquote en línea 138: `border-l-4 border-primary` |
| Decorative grid backgrounds | ✅ | Línea 164: `bg-[radial-gradient(#4f46e504_1px,transparent_1px)]` |

**Veredicto**: Probabilidad MUY ALTA de diseño asistido por IA generativa. La acumulación de 8 señales simultáneas es casi imposible en diseño humano consciente. Un diseñador humano elegiría 2-3 de estos patrones; tenerlos todos indica generación por template.

---

## Overall Impression

Visualmente pulido y coherente en paleta, pero sobrecargado de patrones decorativos AI-gen que restan personalidad. La página se siente genérica a pesar del contenido espiritual único — parece el landing de una startup SaaS, no la portada de una ONG de despertar de consciencia. La jerarquía es buena, pero hay demasiadas secciones (10) compitiendo por atención.

---

## What's Working

1. **Reproductor de audio integrado**: El waveform animado + marquee + seek bar es un detalle técnico notable que aporta valor real y sensación de "app viva". La transición con Vue `<Transition>` está bien implementada.
2. **FAQ con `<details>` nativo**: Buena decisión técnica — accesible, funcional sin JS, consistente con DaisyUI. Las respuestas están bien escritas, sin jerga excesiva.
3. **Uso coherente de colores DaisyUI**: `primary`, `secondary`, `base-content` usados consistentemente. La tematización es sólida y permitiría cambiar de tema sin romper el diseño.

---

## Priority Issues

### P1 — Sección oculta con `hidden` (línea 91)
La sección "¿Por dónde empezar?" está hardcodeada como oculta. Es dead code que sugiere una decisión de diseño a medias. Decidir: eliminarla o activarla con un flag condicional.

### P1 — Exceso de patrones AI-gen erosionan la identidad de marca
8/9 anti-patrones detectados. La portada parece genérica. La ONG TSEYOR tiene una identidad única (guías estelares, consciencia, comunidades armónicas) pero el diseño dice "SaaS startup". Urge una revisión de identidad visual.

### P2 — Sin estado de error para props vacías
Si `stats.libros` es undefined, `formatearNumero` devuelve "0", pero si `paginasFilosofia` llega vacío, se renderiza un grid vacío con solo el botón "Explorar todos los temas". Igual con `entradasRecientes` — grid vacío sin feedback.

### P2 — Hero image no lazy-loadable
La imagen de fondo del hero (línea 4) usa `background-image: url(...)` inline. Esto impide lazy-loading nativo del navegador, añadiendo peso al primer paint sin control.

### P3 — Sin foco visible personalizado
Los botones usan `cursor-pointer` y transiciones pero no hay estilos `:focus-visible` explícitos. Con DaisyUI podría heredarse, pero debería verificarse en modo teclado.

---

## Persona Red Flags

### Jordan (First-Timer)
- El eyebrow "CONFEDERACIÓN DE MUNDOS HABITADOS DE LA GALAXIA" (línea 126) abruma. Jordan llega sin contexto y recibe un muro de conceptos.
- 10 secciones sin guía ni onboarding. Jordan no sabe por dónde empezar (la sección que justo dice "¿Por dónde empezar?" está oculta).
- La jerga ("telepatía externa", "canalizador Chac-Mool Puente") en FAQ asume demasiado conocimiento previo.

### Sam (Accessibility)
- El contraste del texto `text-base-content/60` (usado extensivamente en descripciones) puede no alcanzar WCAG AA sobre `bg-base-100`.
- Las animaciones `animate-waveform`, `animate-marquee` y `animate-pulse` no respetan `prefers-reduced-motion` — solo Lenis lo chequea (línea 556).
- Los botones no tienen `aria-label` descriptivo. `<img alt="" />` en la libélula es correcto (decorativo), pero otras imágenes podrían beneficiarse de alt text más descriptivo.

### Casey (Mobile User)
- La sección "Medita ahora" usa `min-h-[500px] sm:min-h-[740px] max-h-[90vh]` (línea 251) — en mobile esto puede ocupar toda la pantalla y más, con riesgo de que el contenido quede cortado.
- El grid de stats (línea 49) usa `sm:grid-cols-2 lg:grid-cols-4` — en móvil 1 columna, pero cada card tiene icono + dos líneas de texto que pueden verse apretadas.
- `rounded-4xl` (32px) en móvil ocupa espacio útil en esquinas.

---

## Minor Observations

- El `v-if="entradasRecientes?.length"` (línea 399) usa optional chaining, correcto, pero `entradasRecientes` tiene default `[]` — el optional chaining es redundante pero inofensivo.
- El CSS `@keyframes` marquee duplica el contenido con un separador invisible (`opacity-0` línea 301) para crear el efecto — solución creativa pero frágil en distintos anchos de viewport.
- La función `formatearNumero` usa `toLocaleString('es-ES')` — buen detalle de localización.
- Hay SVG inline (icono de reloj en stats, línea 52) que no forma parte de Lucide — inconsistencia en el set de iconos.
- La imagen `libelula.png?w=180` usa query params que sugieren un servicio de imágenes (Glide/Intervention) — bien, pero el hero background no usa este servicio.

---

## Questions to Consider

1. ¿Cuál es la acción principal que queremos que un first-timer realice en los primeros 10 segundos? Actualmente hay 3 CTAs en el hero (Explorar, Biblioteca, Eventos condicional) + 4 stats links + secciones abajo. Demasiadas opciones.
2. ¿Realmente necesitamos las 10 secciones en la portada? ¿Podríamos reducir a 5-6 y mover el resto a subpáginas?
3. ¿La identidad visual actual refleja "despertar de consciencia" o "startup tecnológica"? ¿Deberíamos explorar una dirección visual más orgánica, con texturas naturales, ilustraciones, y menos glassmorphism?
4. ¿El eyebrow tracking-widest en cada sección aporta jerarquía o ruido? Probar sin ellos en 2-3 secciones y medir.
5. La sección "¿Por dónde empezar?" está oculta — ¿debería reemplazar al hero como experiencia de onboarding para first-timers?
