# Análisis Heurístico — Cursos/Index.vue

**Página**: Cursos | **Framework**: Laravel + Inertia + Vue 3 + Tailwind + DaisyUI  
**Evaluador**: Analista UX/UI Sénior | **Fecha**: 2026-07-15

---

## Design Health Score (10 Heurísticas de Nielsen)

| # | Heurística | Puntaje | Notas |
|---|---|---|---|
| 1 | Visibilidad del estado del sistema | 3/4 | No hay indicadores de carga ni estados vacíos visibles |
| 2 | Relación sistema-mundo real | 4/4 | Lenguaje claro, apropiado para la audiencia |
| 3 | Control y libertad del usuario | 2/4 | Sin navegación interna dentro de la página (anclas, breadcrumbs) |
| 4 | Consistencia y estándares | 3/4 | Componentes reutilizados consistentemente (TextImage, Section) |
| 5 | Prevención de errores | 1/4 | Sin validación de props ni estados de error (l. 107) |
| 6 | Reconocimiento antes que recuerdo | 3/4 | Secciones bien diferenciadas visualmente |
| 7 | Flexibilidad y eficiencia de uso | 2/4 | Sin atajos ni navegación saltable entre bloques |
| 8 | Diseño estético y minimalista | 3/4 | Buena jerarquía, pero la sección oculta (l. 68) hincha el template |
| 9 | Ayuda a usuarios a reconocer errores | 1/4 | Sin feedback de error si `proximosCursos` falla |
| 10 | Ayuda y documentación | 2/4 | Sin microcopys orientativos para el usuario |

**Total: 24/40** — Salud de diseño moderada. Problemas de robustez y prevención de errores arrastran el puntaje.

---

## Anti-Patterns Verdict

| Anti-Patrón | Presente | Gravedad |
|---|---|---|
| Dead Code en Producción | ✅ (l. 68, `v-if="false"`) | 🔴 CRÍTICO |
| Missing Import en Runtime | ✅ (l. 107, `computed` no importado) | 🔴 CRÍTICO |
| Prop sin Tipo | ✅ (l. 99, `proximosCursos` sin `type`) | 🟡 MODERADO |
| Enlace Roto por Hardcode | ✅ (l. 82, `href="donde-estamos"` sin binding) | 🟡 MODERADO |
| Punto Fuera del Tag | ✅ (l. 84, `</p>.`) | 🟢 LEVE |

---

## Overall Impression

La página tiene una identidad visual sólida y los bloques de contenido están bien diferenciados. Sin embargo, el componente está "maquillando" su funcionalidad principal: la sección de cursos próximos está desactivada permanentemente con un `v-if="false"`. El usuario ve una landing promocional pero NO puede acceder a la funcionalidad que el nombre de la página promete. Esto es engañoso y genera una brecha entre expectativa y realidad.

---

## What's Working (2-3)

1. **Jerarquía visual consistente**: Los bloques `Section` > `TextImage` / `FeatureColumns` mantienen un ritmo visual predecible y escaneable. El usuario puede hacer scroll y entender la propuesta de valor.

2. **Componentes reutilizados**: `TextImage` aparece tres veces con distintas configuraciones — buen uso de composición. La API del componente (`image-right`, `buttonLabel`, `href`) es flexible y consistente.

3. **Contenido alineado con el propósito**: Los cuatro features (Exploración Cósmica, Desarrollo personal, Extrapolación mental, Cuántica y Conciencia) cubren bien los pilares temáticos del curso.

---

## Priority Issues (3-5 con P0-P3)

### P0 — Dead Code en Producción (l. 68)
```vue
<Section v-if="false" class="py-12 space-y-12">
```
El bloque completo de "Próximos cursos" está desactivado permanentemente. Esto es código muerto que:
- Engaña al usuario (el título de la página es "Cursos" pero no hay cursos visibles)
- Hincha el bundle sin beneficio
- Duplica lógica de ruteo (`route('eventos')`, `route('donde-estamos')`)
- Crea un enlace roto (l. 82: `href="donde-estamos"` sin binding dinámico)

**Riesgo**: Alto. El nombre de la página promete funcionalidad que no existe.

### P0 — `computed` sin Import (l. 107)
```js
const cursos = computed(() => props.proximosCursos.map(...))
```
`computed` no está importado desde Vue. Dependiendo de la configuración del bundler, esto puede lanzar un ReferenceError en runtime o funcionar de casualidad si Vue lo expone globalmente.

### P1 — Prop `proximosCursos` sin validación de tipo (l. 99-101)
```js
proximosCursos: {
    required: true
}
```
Si el backend envía `null` o un objeto no iterable, `props.proximosCursos.map()` en l. 107 explota sin control. Sin `type: Array`, Vue no puede advertir en desarrollo.

### P2 — Enlace "donde-estamos" hardcodeado sin binding (l. 82)
```html
<Link href="donde-estamos">
```
Sin `:href`, esto se trata como una ruta literal `/donde-estamos` (o string plana sin procesar por Inertia). Debería ser `:href="route('donde-estamos')"` como en el resto de la página (l. 76).

### P3 — Punto fuera del párrafo (l. 83-84)
```html
</p>.
```
El punto final cierra fuera del `</p>`. Es un error menor de marcado pero denota falta de revisión en el template.

---

## Persona Red Flags (2-3)

### 👤 Visitante nuevo que explora la oferta formativa
Toca "Cursos" en la navegación porque quiere ver la oferta disponible. Encuentra una landing promocional pero **ningún listado de cursos, fechas ni próximas convocatorias**. La sección relevante (`v-if="false"`) no existe para él. Abandona confundido.

### 👤 Usuario recurrente que busca el siguiente curso
Vuelve a la página esperando ver fechas, horarios o cupos disponibles. El bloque fantasma de "Próximos cursos" le da esperanza visual (si inspecciona, podría ver el markup oculto), pero nunca se renderiza. Frustración garantizada.

### 👤 Administrador que gestiona contenido
Sube cursos desde el backend (poblando `proximosCursos`) — pero la página nunca los muestra porque la sección está hardcodeada a `false`. El admin cree que su contenido se publica, pero en realidad no. Bug silencioso de publicación.

---

## Minor Observations

- **Espaciado excesivo (l. 10-12)**: Líneas en blanco duplicadas entre bloques (`</Section>\n\n\n<Section`). No afecta al render pero perjudica legibilidad del template.
- **Alt text en imágenes**: El componente `TextImage` l. 5 usa `srcImage` pero no hay señal de atributo `alt` para accesibilidad. La imagen decorativa del curso necesita texto alternativo.
- **Video sin etiqueta accesible (l. 14)**: `VideoPlayer` embebe un iframe de YouTube sin `title` ni `aria-label`. Usuarios de lector de pantalla no sabrán qué contiene.
- **Múltiples `TextImage` sin variación**: Tres instancias del mismo componente con la misma orientación (`image-right` dos veces) pueden crear sensación de monotonía visual.

---

## Questions to Consider

1. ¿Debe esta página ser una **landing promocional** (renombrarla a `/curso-holistico`) o un **listado funcional de cursos** (reactivar la sección oculta)? El nombre actual "Cursos" implica lo segundo; el contenido actual es lo primero.
2. ¿Por qué `proximosCursos` se pasa como prop si su única representación visual está desactivada? ¿Hay un plan para reactivarla, o es un artefacto de un sprint anterior?
3. ¿Se ha considerado un **estado vacío** amigable ("Próximamente anunciaremos nuevas fechas") en lugar de ocultar toda la sección?
