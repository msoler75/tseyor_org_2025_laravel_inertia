---
target: resources/js/Pages/Ong/Index.vue
total_score: 20
p0_count: 1
p1_count: 3
timestamp: 2026-07-19T20-29-00Z
slug: resources-js-pages-ong-index-vue
---
# Critique: ONG/Index.vue

## Design Health Score

| # | Heuristic | Score | Key Issue |
|---|-----------|-------|-----------|
| 1 | Visibility of System Status | 2 | Carousel sin indicador de pausa ni feedback de carga de imagenes |
| 2 | Match System / Real World | 4 | Voz humana, autentica, lenguaje ONG apropiado en espanol |
| 3 | User Control and Freedom | 2 | Carousel sin pausa, sin teclado, sin swipe. Sin back-to-top |
| 4 | Consistency and Standards | 2 | 3 estilos de boton secundario incompatibles entre si |
| 5 | Error Prevention | 2 | Auto-avance sin pause-on-hover ni fallback de imagenes |
| 6 | Recognition Rather Than Recall | 3 | Navegacion sticky bien etiquetada; dots del carousel sin aria-label |
| 7 | Flexibility and Efficiency | 1 | Sin atajos de teclado, sin swipe, sin busqueda in-page |
| 8 | Aesthetic and Minimalist Design | 2 | Scaffolding repetitivo + multiples fallos de contraste |
| 9 | Error Recovery | 1 | Sin fallback si las imagenes 404 del carousel |
| 10 | Help and Documentation | 1 | Sin ayuda contextual ni tooltips en puntos de decision |
| **Total** | | **20/40** | **Acceptable (20-27)** |

## Anti-Patterns Verdict

### LLM assessment: SUSPECT

La pagina no es "obviamente AI", pero hay patrones acumulativos que delatan:

- Eyebrow en TODAS las secciones: `text-xs font-bold uppercase tracking-[0.3em]` repetido en 4/4 secciones de contenido. Un kicker deliberado es voz; un eyebrow en cada seccion es gramatica AI.
- Grids de cards identicos en ambitos y proyectos: misma plantilla `grid sm:grid-cols-2`, mismas cards `rounded-2xl bg-base-100 border p-6 shadow-sm`. Dos grids indistinguibles.
- Iconos decorativos con gradiente repetidos 5 veces (`bg-gradient-to-br from-primary/20 to-primary/5`): cajas de icono de 56x56px sobre cada heading.
- Transiciones conversacionales repetidas 3 veces seguidas.

Lo que salva: el copy es genuinamente bueno, la voz es distintiva, y la navegacion sticky con IntersectionObserver es un detalle que ningun template generaria.

### Deterministic scan

El detector CLI devolvio 0 hallazgos en ambos archivos -- falso negativo: el motor de regex no parsea <template> de Vue SFC. Todos los hallazgos son de revision manual. 8 issues manuales encontradas (3 P0, 5 P2).

## Overall Impression

El copy es el activo mas fuerte -- voz calida, filosofica y honesta que construye credibilidad. La arquitectura de informacion funciona. Pero el diseno visual esta anclado en scaffolding repetitivo que aplana la jerarquia, y la pagina pide confianza (dinero, tiempo) sin haberla ganado.

## What's Working

1. Copy y voz editorial: "No actuamos desde una posicion de superioridad ni como poseedores de respuestas definitivas" -- distintivo, honesto, genera confianza.
2. Sidebar navigation con IntersectionObserver: scroll-tracking con activeSection reactivo + actualizacion de URL hash + adaptacion mobile via select.
3. Tipografia con escalado responsive: 14px base + breakpoints + clamp() en headings.

## Priority Issues

### [P0] Carousel: dots de 8x8px sin aria-label, sin pausa, sin foco
Los botones de navegacion miden 8x8px -- 5.5x por debajo del minimo WCAG de 44px. Sin aria-label. setInterval sin pausa. Violacion WCAG 2.2.2.
Fix: min-w-[44px] min-h-[44px] + p-2, aria-label="Slide N", pause on hover/focus, keydown arrows, prefers-reduced-motion.
-> $impeccable adapt resources/js/Components/Carousel.vue

### [P1] Cero fotografia real en secciones de contenido
Una ONG pidiendo donaciones sin mostrar evidencia visual. La seccion de Voluntariado muestra un icono decorativo gigante donde deberian ir fotos de voluntarios reales.
Fix: reemplazar placeholder con fotos reales, agregar testimonios con foto y nombre, barra de metricas de impacto.
-> $impeccable bolder resources/js/Pages/Ong/Index.vue

### [P1] Sin senales de confianza antes de los CTAs de conversion
"Donar ahora" aparece en el hero antes de que la organizacion demuestre nada. Sin testimonios, metricas, badges de transparencia.
Fix: mover CTA de donacion despues de seccion de credibilidad, agregar numeros de impacto, testimonios de voluntarios.
-> $impeccable harden resources/js/Pages/Ong/Index.vue

### [P1] Scaffolding identico en las 4 secciones aplana la jerarquia visual
Las cuatro secciones repiten: icono 56x56 con gradiente -> eyebrow tracked -> h2 -> texto -> cards/CTA. Para la tercera seccion, el usuario ya no lee.
Fix: quitar eyebrow de 2+ secciones, reducir iconos decorativos, darle a Unete tratamiento visual de climax.
-> $impeccable layout resources/js/Pages/Ong/Index.vue

### [P2] Sistema de botones inconsistente
Secundarios en 3 estilos incompatibles: btn-secondary, bg-base-100 border, y bg-white/10 border-white/20.
-> $impeccable polish resources/js/Pages/Ong/Index.vue

### [P2] Multiples fallos de contraste de color
text-base-content/60, /50, text-primary/60, text-white/70 -- varios por debajo de 4.5:1.
-> $impeccable colorize resources/js/Pages/Ong/Index.vue

## Persona Red Flags

### Jordan (First-Timer)
Entra, ve carousel, lee headline. Cards identicas -- no distingue si ya leyo esto. Llega a "Quiero colaborar" sin haber visto una cara, una foto, un numero de impacto. Abandona.

### Casey (Mobile)
Dots del carousel 8x8px -- imposibles de tocar. Cards densas hacen scroll eterno. Sin boton volver arriba.

### Riley (Stress Tester)
6 imagenes del carousel sin fallback si 404. IntersectionObserver sin polyfill para Safari viejo.

## Minor Observations

- scroll-mt-24 asume header de exactamente 6rem
- backdrop-blur-sm en boton del CTA final es glassmorphism fragil
- Voluntariado usa hidden md:flex -- en mobile no hay reemplazo visual
- Texto text-sm con base 14px = ~12.25px, ilegible para algunos
- pointer-events-none en overlay del carousel correcto, verificar z-index de dots

## Questions to Consider

- Si tuvieras que reemplazar el carousel por UNA sola foto hero que capture lo que es TSEYOR, cual seria?
- Que pasaria si quitas TODOS los eyebrows y TODOS los iconos decorativos y dejaras que los headings carguen la pagina solos?
- El CTA final dice "No buscamos seguidores. Buscamos personas." El diseno refleja esa filosofia o parece un template de conversion?
