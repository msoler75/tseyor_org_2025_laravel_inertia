# Análisis Heurístico — ONG Index.vue

## Design Health Score (10 heurísticas Nielsen, /40)

| # | Heurística | Puntuación | Notas |
|---|-----------|------------|-------|
| 1 | **Visibilidad del estado del sistema** | 2/4 | Sin loader/indicador al navegar entre secciones (línea 48 anchor `#proyectos`). El usuario no recibe feedback visual de que el scroll está ocurriendo. |
| 2 | **Relación sistema-mundo real** | 4/4 | Lenguaje claro, tono institucional pero cercano. "Todos aportamos. Todos aprendemos." Metáforas coherentes con el dominio ONG. |
| 3 | **Control y libertad del usuario** | 2/4 | No hay navegación interna sticky — el usuario debe scrollear manualmente 5 viewports completos. El anchor `#proyectos` (línea 48) es la única navegación intra-página. Sin "volver arriba". |
| 4 | **Consistencia y estándares** | 3/4 | Patrón de secciones consistente (label → h2 → p → CTA). Sin embargo, las `card` de misión/proyectos son `div` con `divide-y` sin interactividad, rompiendo la expectativa de que una lista vertical es clickeable (líneas 71-76, 111-116). |
| 5 | **Prevención de errores** | 3/4 | No aplica formularios. Riesgo bajo. El `v-if="estatutosUrl"` (línea 84) previene enlaces rotos condicionalmente. |
| 6 | **Reconocimiento antes que recuerdo** | 2/4 | 5 secciones de contenido denso sin barra de progreso ni indicador de posición. El usuario debe recordar dónde está leyendo. |
| 7 | **Flexibilidad y eficiencia** | 1/4 | Cero atajos. Sin skip-to-content, sin tabla de contenidos, sin sticky nav. Solo un anchor `#proyectos`. El contenido es puramente lineal. |
| 8 | **Diseño estético y minimalista** | 3/4 | Tipografía cuidada, jerarquía clara. El logo JPG (línea 13) puede pixelarse en retina — formato raster en vez de SVG o WebP optimizado. |
| 9 | **Ayuda a reconocer errores** | 1/4 | No aplica directamente, pero la ausencia total de feedback para acciones (donar, buscar centro, voluntariado) es preocupante. Los CTAs no muestran estado de carga ni confirmación. |
| 10 | **Ayuda y documentación** | 1/4 | Sin microcopy de ayuda en CTAs. "Donar ahora" no anticipa qué pasará (¿modal?, ¿redirect?, ¿formulario?). El link "Conocer nuestros proyectos" (línea 48) lleva a un anchor interno sin indicar que es scroll. |

**Total: 22/40** — Riesgo medio-alto. Pierde puntos en eficiencia, feedback y navegación.

---

## Anti-Patterns Verdict

| Anti-Patrón | Severidad | Evidencia |
|------------|-----------|-----------|
| **Wall of Text institucional** | Alta | 5 párrafos consecutivos sin bullet points, tablas, iconos ni elementos visuales rompen la escaneabilidad (líneas 27-35, 66-69, 131-139, 167-170). |
| **CTA sin microcopy ni estado** | Alta | "Donar ahora", "Buscar un centro", "Quiero colaborar" no anticipan contexto post-click. Sin loading state, sin confirmación, sin transición visible. |
| **Layout homogéneo sin diferenciación visual** | Media | Las 5 secciones usan exactamente el mismo padding, misma tipografía, mismo patrón label+h2+p. El ojo se fatiga. No hay cambio de ritmo (full-width, bg alterno, imágenes, iconos decorativos). |
| **Anchor como único recurso de navegación** | Media | `#proyectos` es el único mecanismo intra-página. El usuario no puede saltar a Voluntariado, Misión o Únete. |
| **Hardcode de rutas vs route()** | Baja | Las líneas 38 (`/donar`), 42 (`/centros`), 181 (`/libros/...`) usan strings literales mientras la línea 174 usa `route()`. Inconsistencia en el sistema de rutas. |

---

## Overall Impression

Página institucional con **buena base tipográfica y jerarquía de contenidos**, pero plana en ejecución. Se lee más como un documento que como una experiencia web. El diseño trata todas las secciones con el mismo patrón, sin picos visuales ni momentos de respiro. Para una ONG que comunica servicio, acompañamiento y comunidad, la página carece de calidez visual: no hay fotografías reales de personas, no hay iconografía que refuerce los valores, no hay variación de layout que traduzca visualmente el mensaje de diversidad que predica.

---

## What's Working

1. **Jerarquía tipográfica excelente** — La combinación `font-display font-black` en h1/h2 con `tracking-[0.28em]` en labels crea una arquitectura de información clara y legible.
2. **Responsive bien resuelto** — `text-4xl sm:text-5xl lg:text-6xl`, padding y márgenes progresivos. No hay roturas visibles en el grid.
3. **CTA primary/secondary diferenciados** — Botones con roles visuales distintos (`btn-primary` shadow-lg vs `btn-secondary` / `bg-base-100 border-base-300`).

---

## Priority Issues

### P0 — Falta total de feedback post-click en CTAs
**Dónde**: Líneas 38-41, 42-45, 148-152, 172-179, 180-186
**Problema**: Todos los botones enlazan directamente sin indicar carga, éxito, ni error. En conexiones lentas, el usuario siente que "no pasó nada".
**Impacto**: Conversión, confianza.

### P1 — 5 secciones con el mismo layout sin diferenciación visual
**Dónde**: Líneas 10-53, 55-93, 95-118, 120-154, 156-189
**Problema**: Cada sección es un `max-w-4xl`, mismo padding vertical (`py-16 md:py-24`), mismo patrón label/título/texto. Sin variación de ancho, color de fondo, ni elementos gráficos.
**Impacto**: Fatiga de lectura, baja escaneabilidad, experiencia monótona.

### P1 — Logo en JPG raster
**Dónde**: Línea 13
**Problema**: `/archivos/logos/logo-ong-mundo-armonico.JPG`. Formato JPG con pérdida para un logo. Sin variante SVG ni WebP. En pantallas retina se verá pixelado.
**Impacto**: Percepción de marca, calidad visual.

### P2 — Sin navegación sticky ni tabla de contenidos
**Dónde**: Todo el componente
**Problema**: La página tiene 5 secciones de scroll completo. Sin sticky nav, sin indicador de progreso, sin jump-links. El usuario靠 scrolleo puro.
**Impacto**: Usabilidad en mobile, abandono temprano.

### P2 — Anchor `#proyectos` sin offset visible consistente
**Dónde**: Línea 97 — `scroll-mt-24`
**Problema**: El offset está resuelto con Tailwind, pero la navegación desde el link de la línea 48 no tiene transición ni indicación visual de que el scroll ocurrió.
**Impacto**: Desorientación contextual.

---

## Persona Red Flags

### 1. Marta (55, donante ocasional, poca paciencia digital)
Busca información clara y confiable antes de donar. Llega a la página y se encuentra un muro de texto. No ve fotos de proyectos reales, ni impacto concreto, ni testimonios. Los CTAs no le dan confianza post-click.
**Riesgo**: Abandono sin donar.

### 2. Carlos (28, voluntario potencial, mobile-first)
Scrollea en su móvil y todas las secciones se ven igual. No hay un sticky nav que le permita saltar directamente a "Voluntariado". El texto es denso y no hay iconos que ayuden a escanear.
**Riesgo**: No llega al CTA de voluntariado (línea 177).

### 3. Ana (45, coordinadora de otra ONG, busca colaboración)
Quiere entender rápido quiénes son y qué hacen. No encuentra datos concretos: número de voluntarios, proyectos activos, impacto. La página es genérica.
**Riesgo**: No percibe profesionalismo ni trayectoria.

---

## Minor Observations

- **Línea 13**: `alt="ONG Mundo Armónico TSEYOR"` — el alt podría describir qué muestra la imagen, no solo repetir el nombre de la ONG.
- **Línea 18**: `tracking-[0.28em]` — valor arbitrario. Si la familia tipográfica cambia, habrá que ajustar manualmente. Mejor una clase utilitaria consistente.
- **Línea 72, 112**: Las listas de ámbitos y proyectos usan `v-for` sobre arrays hardcodeados en el setup (líneas 208-244). Sin data real desde backend — contenido estático que cualquiera puede editar pero que debería venir del CMS.
- **Línea 48**: `Conocer nuestros proyectos →` — la flecha como carácter unicode puede renderizar distinto según SO/font.
- **Línea 174**: `route('cursos.inscripcion.nueva')` vs líneas 38, 42, 181 con strings planas — mezcla de estilos que puede generar errores si se cambia la estructura de rutas.

---

## Questions to Consider

1. ¿Se ha testeado esta página con usuarios reales midiendo tasa de clics en CTAs vs tasa de scroll hasta el footer?
2. ¿Hay datos de analytics mostrando dónde abandonan los usuarios? Sospecho que la mayoría no llega a "Únete al equipo" (última sección).
3. ¿Por qué los ámbitos y proyectos son datos estáticos en el frontend en vez de servirse desde el backend?
4. ¿Se ha considerado una navegación sticky con jump-links a las 5 secciones?
5. ¿Hay planes de incluir fotografía real, testimonios, o métricas de impacto (voluntarios, beneficiarios, proyectos activos)?
