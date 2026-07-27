# Análisis de Critique — Filosofía TSEYOR (`Filosofia.vue`)

## Design Health Score

| # | Heurística (Nielsen) | Puntaje | Notas |
|---|----------------------|---------|-------|
| 1 | **Visibility of System Status** | 3/4 | Sticky sidebar con `activeSection` tracking vía IntersectionObserver. El marcador activo cambia de color (bg-primary + texto blanco) y el highlight en nav se actualiza en tiempo real. Pérdida: no hay indicador de progreso de lectura (scroll %) y al colapsar a móvil el sidebar desaparece sin alternativa visible. |
| 2 | **Match System / Real World** | 3/4 | Icons (planet, sparkle, eye, users-three) emparejan bien con cada tema espiritual. Lenguaje accesible y cálido, coherente con la audiencia. Pérdida: el término "Rayo Sincronizador", "Juul", "atlante" y "especialización" aparecen sin explicación previa — el lector nuevo puede sentirse excluido. |
| 3 | **User Control and Freedom** | 2/4 | Sidebar sticky permite navegación directa entre secciones. Hay un link de "Descubrir la visión general" al inicio. Pero: no hay "volver arriba", no hay breadcrumbs, y al llegar desde otro sitio no hay forma de saber dónde estás dentro del árbol del sitio. La navegación es lineal sin salidas laterales. |
| 4 | **Consistency and Standards** | 3/4 | Patrón consistentísimo entre las 4 secciones narrativas: icono gradiente → eyebrow (tracked uppercase) → h2 → párrafos → callout box → "ideas esenciales" con pills. La repetición es predecible y funciona. Pérdida: el sidebar usa `rounded-xl` y `rounded-lg` mientras los cards usan `rounded-3xl` — micro-inconsistencia en radios. |
| 5 | **Error Prevention** | 3/4 | No hay formularios ni inputs, bajo riesgo de error. Los anchor links son válidos. La única interacción riesgosa es la navegación — y funciona. Pérdida: los href en el nav son `#ancla` y las secciones usan `id`, pero no hay manejo de hash en URL (no se puede compartir una sección específica por URL). |
| 6 | **Recognition Rather Than Recall** | 2/4 | El sidebar reduce carga cognitiva mostrando siempre las 4 secciones. Pero el contenido narrativo es extenso y denso; el usuario debe recordar conceptos entre secciones (Guías Estelares, Plan Cósmico, etc.) sin resúmenes acumulativos. Las "ideas esenciales" ayudan, pero están al final de cada bloque cuando ya leíste todo. |
| 7 | **Flexibility and Efficiency** | 1/4 | No hay shortcuts, no hay búsqueda dentro de la página, no hay vista resumen/expand, no hay print-friendly layout. Usuarios power users no tienen forma de acelerar la navegación más allá del sidebar. |
| 8 | **Aesthetic and Minimalist Design** | 2/4 | Visualmente limpio y con buena jerarquía tipográfica. Pero el contenido es EXTREMADAMENTE denso: ~40 párrafos + 4 callouts + 4 sets de pills. Hay duplicación conceptual (las ideas esenciales repiten contenido del texto). El diseño es minimalista en UI pero maximalista en contenido. |
| 9 | **Error Recovery** | 2/4 | No hay operaciones destructivas. Pero: si el IntersectionObserver falla (JS deshabilitado, error en script), no hay fallback — el sidebar no marcaría nada. Tampoco hay manejo de error si `route()` falla en los botones CTA. |
| 10 | **Help and Documentation** | 1/4 | No hay tooltips, no hay "¿qué significa esto?", no hay glosario, no hay ayuda contextual. El `cursor-help` en las `<Referencia>` pills sugiere que debería haber un tooltip — pero no lo hay en el markup visible. El usuario hace hover y no obtiene información adicional. |

**Total: 22/40** — Diseño visualmente sólido pero con carencias significativas en flexibilidad, ayuda contextual y manejo de carga cognitiva.

---

## Anti-Patterns Verdict

Riesgo de apariencia AI-generated: **MODERADO-ALTO** — varios marcadores presentes:

| Anti-Pattern | Estado | Evidencia |
|---|---|---|
| Gradient text | ❌ No presente | El texto usa `text-primary` sólido, no gradientes directos en tipografía. |
| Glassmorphism decorativo | ⚠️ Presente | Los callouts tipo `bg-base-100/60 border border-base-300/30` (línea 29, 115, 180, etc.) son glassmorphism suave — no excesivo, pero sigue el patrón. |
| Hero-metrics template | ⚠️ Parcial | Hero section (líneas 10-46) sigue la estructura: pre-title eyebrow → h1 con highlight → párrafo → cita destacada → link CTA. Es una plantilla, no metrics, pero el molde es reconocible. |
| Card grids idénticos | ✅ Sí, evidente | Las 4 cards de `camino` (líneas 210-216) son idénticas en estructura: icono contenedor `rounded-xl` → h3 → p. Solo cambia el contenido. |
| Tiny uppercase tracked eyebrow | ✅ SÍ, patrón dominante | Cada sección usa `text-xs font-bold uppercase tracking-[0.3em] text-primary/60`. Es el marcador más fuerte de plantilla AI: aparece en línea 13, 53, 82, 133, 198, 248, 310. |
| Numbered section markers | ✅ Sí | Sidebar (líneas 63-68): índice numérico `{{ index + 1 }}` en contenedor `w-8 h-8 rounded-lg`. |
| border-radius excesivo | ✅ Sí | `rounded-3xl` (1.5rem/24px) en cards de contenido (líneas 29, 115, 166, etc.), `rounded-2xl` (16px) en contenedores de iconos (líneas 79, 130, etc.). DaisyUI recomienda `rounded-box` (12px) para cards — 24px es alto. |
| Side-stripe borders | ⚠️ Leve | Algunos callouts tienen `border-l` y laterales, pero no es un patrón dominante. |
| Decorative grid backgrounds | ❌ No presente | No se usa `bg-grid` ni patrones de cuadrícula decorativos. |

**Veredicto**: El componente muestra al menos 5 de 9 anti-patrones evaluados. La estructura "eyebrow → h1/h2 → párrafos → callout → pills" se repite 4 veces de forma casi idéntica. Esto NO significa que sea AI-generated (puede ser un componente bien diseñado con un patrón consistente), pero hay señales que un revisor experimentado identificaría como "demasiado plantillizado".

---

## Overall Impression

La página Filosofía es visualmente atractiva y comunica bien su tono espiritual y reflexivo. La tipografía (font-display + tracking) y la paleta de color primario crean una atmósfera coherente. El sticky sidebar con IntersectionObserver es un acierto funcional. Sin embargo, la página sufre de **sobrecarga de contenido**: es muy larga, muy densa, y repite el mismo patrón estructural 4 veces sin variación visual que marque progresión narrativa. La experiencia en móvil se resiente porque el sidebar colapsa y el usuario pierde toda orientación.

---

## What's Working

1. **Sticky Index + IntersectionObserver** (líneas 52-73, 365-381): La navegación lateral con tracking activo es funcional y elegantemente implementada. El cambio de color en el ítem activo da feedback claro.

2. **Jerarquía tipográfica**: La escala `text-5xl sm:text-6xl lg:text-7xl` para h1, `text-3xl sm:text-4xl` para h2, `text-lg` para lead, `text-base` para cuerpo está bien calibrada. `font-display font-black` con `tracking-tight` funciona.

3. **Callout boxes variados**: Aunque el patrón se repite, hay variación visual entre los callouts: unos son glassmorphism (bg-base-100/60), otros tienen gradiente (bg-gradient-to-br from-primary/10), y el de "Sociedades Armónicas" (línea 283) usa un gradiente más fuerte que funciona como clímax visual.

---

## Priority Issues

### P0 — Sidebar desaparece en móvil sin alternativa (líneas 52-73)
- **Problema**: El nav lateral solo se muestra en `lg:grid`. En móvil/tablet el usuario pierde todo acceso al índice de secciones.
- **Impacto**: En móvil, el usuario debe hacer scroll manual por ~500 líneas de contenido sin orientación ni atajos.
- **Recomendación**: Implementar un "sticky bottom nav" o un selector desplegable móvil con las secciones.

### P1 — No hay scroll-snap ni progreso de lectura
- **Problema**: La página es extremadamente larga (4 secciones + hero + CTA). No hay barra de progreso, scroll-snap, ni indicador de "estás aquí" más allá del sidebar (que desaparece en móvil).
- **Impacto**: El usuario no sabe cuánto falta ni dónde está — la deserción por fatiga de scroll es alta.
- **Recomendación**: Añadir un progress bar tipo "reading progress" en el top del viewport, visible en todos los breakpoints.

### P1 — `cursor-help` en pills de "ideas esenciales" sin tooltip asociado (línea 120, 185, 236, 295)
- **Problema**: `<Referencia class="cursor-help">` sugiere que el elemento es interactivo y que al hacer hover aparecería ayuda contextual, pero no hay tooltip, popover ni title attribute.
- **Impacto**: Falsa affordance — el usuario cliquea/hoverea esperando información y no obtiene nada. Experiencia frustrante.
- **Recomendación**: Implementar tooltips nativos con `title` o DaisyUI tooltip en los pills, explicando brevemente cada concepto.

### P1 — No se puede compartir sección específica por URL
- **Problema**: Los anchors `#mensaje-cosmico` etc. funcionan internamente para scroll, pero la página no actualiza `window.location.hash` ni hay manejo de hash en `onMounted`. Si un usuario llega a `#sociedades-armonicas`, el IntersectionObserver parte desde el primer artículo.
- **Impacto**: No se puede compartir enlace directo a una sección específica de la filosofía.
- **Recomendación**: En el `onMounted`, leer `window.location.hash` y hacer scroll forzado al elemento correspondiente antes de inicializar el observer.

### P2 — Contraste insuficiente en textos secundarios
- **Problema**: `text-base-content/65` y `/60` (líneas 89, 204, 215, etc.) reducen la opacidad al 60-65% del color base. En fondos claros (base-100), esto puede caer por debajo de 4.5:1 para texto normal.
- **Impacto**: Usuarios con baja visión o en exteriores tendrán dificultad para leer párrafos completos.
- **Recomendación**: Usar como mínimo `text-base-content/75` para texto corporal secundario.

### P2 — Hero section sin imagen de fondo ni elemento visual diferenciador (líneas 10-46)
- **Problema**: El hero es 100% tipográfico con un callout. No hay imagen, ilustración, pattern ni elemento visual que enganche o comunique la atmósfera espiritual.
- **Impacto**: La página empieza con una "pared de texto". En un sitio espiritual/filosófico, la ausencia de un elemento visual que inspire es una oportunidad perdida.
- **Recomendación**: Un fondo sutil (gradiente, patrón geométrico, o una ilustración) o un video de fondo abstracto.

---

## Persona Red Flags

### 🔴 Alex (Power User)

| Flag | Detalle |
|------|---------|
| Sin búsqueda inline | No puede buscar "Rayo Sincronizador" dentro de la página sin Ctrl+F |
| Sin shortcuts de teclado | No hay atajos para saltar entre secciones |
| Sin vista compacta | Atrapado en el layout narrativo, no hay opción "resumen" |
| Sin print-friendly | No puede exportar la filosofía a PDF para lectura offline |

### 🔴 Jordan (First-Timer)

| Flag | Detalle |
|------|---------|
| Jerga no explicada | "Juul", "atlante", "Rayo Sincronizador", "especialización" aparecen sin definición previa |
| Sin onboarding | No hay "cómo leer esta página" ni guía de navegación |
| Longitud abrumadora | ~500 líneas de contenido denso sin pausas visuales fuertes entre secciones |

### 🟡 Casey (Mobile User)

| Flag | Detalle |
|------|---------|
| Sidebar perdido | El índice sticky desaparece. En su lugar hay 50+ líneas de scroll por sección |
| `rounded-3xl` en móvil | Cards con 24px de border-radius en pantallas de 375px de ancho es desproporcionado |
| Touch targets | Los pills de "ideas esenciales" tienen solo `px-3 py-1.5` — 24px de altura, por debajo del mínimo recomendado de 44px para touch |

---

## Minor Observations

- **Línea 3-4**: `Back` y `Share` están fuera del contenedor `<Sections>`. En pantallas pequeñas, los dos elementos pueden solaparse o quedar muy juntos.
- **Línea 12-44**: El hero usa `max-w-5xl` para el contenedor y `max-w-3xl` para el texto — 5 columnas de ancho para 3 de contenido es muy amplio en desktop.
- **Línea 17**: `text-5xl sm:text-6xl lg:text-7xl` — 7xl en LG puede producir líneas de 1-2 palabras (especialmente "humano y cósmico"). Probar `text-balance`.
- **Línea 167**: `bg-gradient-to-br from-primary/10 to-base-100` — el gradient fade to base-100 es imperceptible porque base-100 ya es el fondo.
- **Líneas 360-468**: `<script setup>` no tiene `import { ref, onMounted } from 'vue'` visible. Si no hay import implícito, esto rompería en runtime.
- **Línea 369**: `IntersectionObserver` puede no estar disponible en navegadores antiguos. No hay polyfill ni fallback.
- Las 4 secciones narrativas no tienen `aria-labelledby` conectado al `h2`.

---

## Questions to Consider

1. ¿El patrón repetitivo (eyebrow → título → párrafos → callout → pills) es intencional como elemento estilístico o es una plantilla no cuestionada?
2. ¿Se ha probado la página con usuarios reales midiendo tasa de lectura completa vs abandono?
3. ¿La decisión de no mostrar el sidebar en móvil fue por espacio o por falta de implementación?
4. ¿Los términos "atlante", "Juul", "especialización" — deberían tener un glosario flotante o un link a definición?
5. ¿Hay analytics mostrando cuántos usuarios hacen clic en el CTA "Explorar todos los temas"?
