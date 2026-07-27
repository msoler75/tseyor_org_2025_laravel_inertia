# Auditoría UX/UI — Cursos/Index.vue

**Página**: Cursos | **Framework**: Laravel + Inertia + Vue 3 + Tailwind + DaisyUI  
**Evaluador**: Analista UX/UI Sénior | **Fecha**: 2026-07-15

---

## Audit Health Score (5 Dimensiones)

| Dimensión | Puntaje | Evaluación |
|---|---|---|
| Funcionalidad | 2/4 | La funcionalidad principal (listar cursos) está desactivada. El componente no hace lo que su nombre sugiere. |
| Consistencia Visual | 4/4 | Los componentes se renderizan de forma homogénea. Uso coherente de espaciados, tipografía y colores del sistema de diseño. |
| Accesibilidad | 1/4 | Sin `alt` texts detectables, sin `aria-label` en el video, jerarquía de títulos no verificable desde el template. |
| Rendimiento Percibido | 3/4 | Sin estados de carga visibles. Si `libro` o `proximosCursos` tardan, el usuario ve blank screen. |
| Código y Mantenibilidad | 2/4 | Dead code, missing import, prop sin tipo, enlace hardcodeado. La deuda técnica es baja en volumen pero alta en impacto potencial. |

**Total: 12/20** — Salud aceptable con riesgo funcional latente. La brecha entre nombre y función real es el hallazgo más grave.

---

## Anti-Patterns Verdict

| Anti-Patrón | Archivo:Línea | Severidad |
|---|---|---|
| Dead Code | `Index.vue:68` — `v-if="false"` oculta toda la sección de "Próximos cursos" | 🔴 Blocker |
| Missing Import | `Index.vue:107` — `computed` usado sin importar desde `vue` | 🔴 Blocker |
| Prop sin Type Guard | `Index.vue:99` — `proximosCursos` sin `type: Array` | 🟡 Mayor |
| Hardcoded Route | `Index.vue:82` — `href="donde-estamos"` sin binding Inertia | 🟡 Mayor |
| Marcado Inválido | `Index.vue:83-84` — Punto fuera de `</p>` | 🟢 Menor |

---

## Executive Summary

**Cursos/Index.vue** es una página promocional del curso holístico de TSEYOR construida con componentes reutilizables y una jerarquía visual limpia. Sin embargo, padece de una **esquizofrenia funcional**: luce como una landing de producto pero se llama "Cursos" (en plural), y contiene un bloque entero de "Próximos cursos" desactivado permanentemente mediante `v-if="false"`.

El código tiene dos errores que deberían impedir cualquier deploy a producción: un `computed` usado sin importar (l. 107) y una sección entera que nunca se renderiza pero ocupa espacio en el bundle (l. 68). A esto se suma una prop sin tipo que, si el backend envía datos inesperados, reventará en runtime sin posibilidad de recuperación.

La buena noticia: la capa visual está sólida. Los componentes `TextImage`, `FeatureColumns` y `Section` se combinan bien. El problema no es de diseño visual sino de **definición funcional y robustez técnica**.

---

## Detailed Findings by Severity

### 🔴 Blocker

#### F1 — Dead Code: Sección "Próximos cursos" permanentemente oculta
- **Línea**: 68 (`<Section v-if="false"`)
- **Impacto**: El componente envía `proximosCursos` como prop requerido (l. 99-101), lo procesa con `computed` (l. 107), pero nunca lo renderiza. Todo el flujo de datos hasta `FeaturedPosts` es código muerto. Si alguien necesita activar esta sección, encontrará además un enlace roto en l. 82.
- **Riesgo**: Alto. El admin que publica cursos desde el CMS cree que la página los muestra. No es así. Bug silencioso de visibilidad de contenido.

#### F2 — `computed` no importado desde Vue
- **Línea**: 107 (`const cursos = computed(...)`)
- **Impacto**: En proyectos Vue 3 con árboles de módulos estrictos, `computed` lanzará `ReferenceError: computed is not defined`. No se declara import en el `<script setup>`.
- **Por qué funciona ahora**: Posiblemente porque el bundler expone Vue globalmente o hay un import implícito en otro lado. Esto es frágil y cambiará si se moderniza la toolchain.
- **Corrección necesaria**: Añadir `import { computed } from 'vue'` en `<script setup>`.

### 🟡 Mayor

#### F3 — Prop `proximosCursos` sin tipo
- **Línea**: 99-101
- **Impacto**: Sin `type: Array`, si el backend envía `null`, `undefined` u otro tipo, `map()` en l. 107 rompe. Sin posibilidad de fallback o estado vacío.
- **Agravante**: El único consumidor de esta prop está en dead code (F1). Esto sugiere que la prop persiste de una implementación anterior inconclusa.

#### F4 — Enlace "donde-estamos" sin binding dinámico
- **Línea**: 82 (`<Link href="donde-estamos">`)
- **Impacto**: Inertia interpreta `href="donde-estamos"` como string literal `/donde-estamos`. El sistema de rutas de Laravel no genera la URL correcta. Enlaces como `route('eventos')` (l. 76) sí usan `:href`.
- **Riesgo**: El enlace está dentro de dead code, pero si se reactiva la sección, el error se vuelve visible.

#### F5 — Sin estados de carga ni error
- **Línea**: 98-105 (todo el script)
- **Impacto**: `proximosCursos` y `libro` son props requeridas pero la página no contempla estados vacíos, de carga ni de error. Si el servidor responde lentamente, el usuario ve una página parcialmente renderizada sin indicación de progreso.
- **Recomendación**: Usar `<Suspense>` o estados condicionales con spinners/skeletons.

### 🟢 Menor

#### F6 — Signo de puntuación mal ubicado
- **Líneas**: 83-84
- **Detalle**: El punto final cierra después de `</p>`. No tiene impacto funcional pero muestra falta de revisión fina.

#### F7 — Líneas en blanco inconsistentes
- **Líneas**: 10-12, 16-19, 43-45, 66-68, 86-89, 92-96
- **Detalle**: Múltiples espacios entre bloques y antes de las etiquetas de script. No afecta al render pero perjudica la legibilidad del código fuente.

---

## Patterns & Systemic Issues

### Patrón: "Maquillaje Funcional"
La página se **presenta** como un listado de cursos (nombre, ruta, prop `proximosCursos`) pero **se comporta** como una landing promocional estática. Este patrón es peligroso porque:
- Genera confusión en mantenimiento futuro ("¿por qué está `v-if="false"`?")
- Oculta bugs (F3, F4) que solo emergerían al reactivar la sección
- Crea una deuda técnica silenciosa que nadie se anima a tocar

### Patrón: Datos sin Render
El componente recibe `proximosCursos` como prop requerido, lo transforma con `computed`, pero nunca lo usa en el template. Este flujo de datos "huérfano" es un code smell que debería eliminarse o reactivarse.

### Tendencia: Buena arquitectura de componentes, débil robustez
Los componentes `TextImage`, `Section`, `FeatureColumns` muestran diseño atómico pensado. El talón de Aquiles está en la **orquestación** — el padre (Index.vue) no maneja bordes, errores ni estados de carga.

---

## Positive Findings

1. **Componentes consistentes y reutilizables**: `TextImage` aparece en tres variantes (hero, filosofía, libro) con la misma API. La propiedad `image-right` permite alternar orientación sin duplicar markup. Buen ejemplo de diseño por composición.

2. **Estilo visual atractivo**: La combinación de `FondoEstrellado` con `VideoPlayer` (l. 12-16) crea una sección multimedia inmersiva que rompe el patrón de `TextImage` y le da ritmo a la página.

3. **FeatureColumns con iconos temáticos**: Los cuatro íconos (alien, lightbulb, rocket, atom) están bien elegidos para cada tema. Refuerzan el mensaje sin necesidad de leer el texto completo.

4. **Layout responsive con Tailwind**: Uso de `container lg:max-w-[1024px]` (l. 4, 49, 57) y `py-12` consistente indica una estructura responsive pensada para desktop y mobile.
