# Impeccable Audit: Navegación Principal TSEYOR

## Audit Health Score (5 Dimensiones)

| Dimensión | Puntaje | Criterios evaluados |
|---|---|---|
| Arquitectura de Información | 3/4 | Jerarquía clara pero demasiado profunda; 50+ enlaces; redundancia en labeling |
| Consistencia Visual & Interacción | 3/4 | UI sólida (underscore, hover, escape), pero CSS mágico frágil y sidebar sin pulir |
| Rendimiento Técnico | 2/4 | `indexOf` matching es frágil, estructura `groups[]` añade complejidad innecesaria, código muerto |
| Accesibilidad | 2/4 | Escape key bien, pero sin focus management, sin ARIA en submenús, sin skip nav, sin breadcrumbs |
| Mobile & Responsive | 2/4 | Sidebar funcional pero accordeon sin límite de profundidad, sin gestos, sin transiciones suaves |

**Total: 12/20**

---

## Anti-Patterns Verdict

| Anti-Patrón | Severidad | Archivo(s) |
|---|---|---|
| Mega-Nav (>50 enlaces, 7 tabs) | Crítico | navigation.js |
| Submenú Fantasma (Blog comentado) | Alto | navigation.js:359-373 |
| Enlace Zombie (disabled sin feedback) | Alto | navigation.js:437, 473 |
| External Link Gaslighting (_self en externos) | Alto | navigation.js:446-448, 467-471 |
| Accordeón Ilimitado (sidebar sin límite) | Medio | NavAside.vue |
| CSS Mágico (nth-child offsets hardcodeados) | Medio | NavTabs.vue:186-200 |
| Código Comentado en Producción (Blog submenu, transiciones) | Bajo | navigation.js, AppLayout.vue |
| `indexOf` URL Matching en lugar de exact match | Alto | nav.js:104-114 |

---

## Executive Summary

La navegación de TSEYOR es técnicamente competente pero arquitectónicamente desbordada. El equipo ha invertido en detalles de calidad (hover con delay, underscore animado, escape key) que demuestran buen criterio de UX. Sin embargo, el menú refleja la estructura interna de la organización (departamentos, secciones) en lugar de la mentalidad del usuario (tareas, objetivos, contenido).

**El problema central**: 50+ enlaces en un solo sistema de navegación. Esto no es un menú — es un sitemap. Un usuario nuevo (Jordan) no puede construir un modelo mental del sitio porque el menú compite consigo mismo. La búsqueda global mitiga parcialmente el problema, pero confiar en search como muleta del nav es síntoma de over-navigation.

**El riesgo inmediato**: El bug de `indexOf` con url="/" es un P0 latente que puede causar estados inconsistentes del tab activo. Los enlaces externos con `_self` son un P0 de UX: el usuario pierde contexto del sitio sin decisión explícita.

---

## Detailed Findings by Severity

### P0 — Crítico (acción inmediata)

#### F1: URL Matching con `indexOf` en `_in()`
**Archivo**: `Stores/nav.js:104-114`
**Problema**: `url.indexOf(tab.url) >= 0` con `tab.url = "/"` matchea TODAS las rutas. Inicio (onlyAside) no se muestra en navbar, pero el método `_updateCurrent` itera sobre TODOS los items incluyendo Inicio. Dependiendo de cómo se use `tab.current` en el sidebar, Inicio podría aparecer siempre como activo o interferir con la detección del tab correcto.
**Riesgo**: Estado inconsistente del tab activo, sidebar confuso.
**Fix**: `url === tab.url || url.startsWith(tab.url + "/")` con excepción para `/`.

#### F2: Enlaces Externos con `target="_self"`
**Archivo**: `navigation.js:446-448` (Muular Electrónico), `navigation.js:467-471` (TSEYOR Canva, actualmente disabled)
**Problema**: Enlaces a otros dominios/subsistemas que navegan en la misma pestaña sin advertencia. El usuario pierde el contexto del sitio TSEYOR.
**Riesgo**: Tasa de rebote artificial, desorientación del usuario.
**Fix**: `target="_blank"` + `rel="noopener noreferrer"` + icono visual de enlace externo.

---

### P1 — Alto (próximo sprint)

#### F3: Items Disabled sin Explicación
**Archivo**: `navigation.js:437` (Publicaciones), `navigation.js:473` (TSEYOR Canva)
**Problema**: Se renderizan como `div` opaco. El usuario los ve, intenta clickear, no pasa nada. Sin tooltip, sin badge, sin feedback.
**Riesgo**: Fricción garantizada, decepción, sensación de "sitio roto".
**Fix**: Badge "Próximamente" o tooltip con `title` explicativo. O eliminar hasta que estén listos.

#### F4: Submenú de Blog Comentado
**Archivo**: `navigation.js:359-373`
**Problema**: El submenú completo está en un bloque `/* */`. El tab existe con `route: "blog"` pero no hay items hijos. Código muerto que sugiere una decisión a medias.
**Riesgo**: Confusión del next developer, posible contenido huérfano.
**Fix**: Restaurar submenú con contenido real o limpiar el código comentado.

#### F5: Sidebar (NavAside) — Profundidad Ilimitada
**Archivo**: `Components/NavAside.vue`
**Problema**: Usa collapse/accordion sin límite. Un usuario puede abrir múltiples secciones simultáneamente, generando scroll infinito. Sin indicador de profundidad, sin breadcrumbs.
**Riesgo**: Desorientación severa en mobile (Casey).
**Fix**: Acordeón con cierre automático al abrir otra sección (accordion mutex), o breadcrumbs.

---

### P2 — Medio (backlog)

#### F6: Redundancia de Labeling
**Problema**: "Biblioteca" → sección "Biblioteca" → item "Biblioteca Tseyor"; "Novedades" → sección "Novedades" → item "Novedades". El título de la sección dentro del submenú repite el nombre del tab.
**Riesgo**: Ruido cognitivo, el usuario procesa información duplicada.
**Fix**: Eliminar la sección cuando solo contiene un grupo con el mismo nombre que el tab.

#### F7: `v-model` Bug en NavAside
**Archivo**: `Components/NavAside.vue:37`
**Problema**: `:v-model="tab.open"` — el `:` hace que Vue lo trate como binding unidireccional, no como v-model bidireccional.
**Riesgo**: El checkbox del accordion no sincroniza correctamente con Pinia.
**Fix**: `v-model="tab.open"` (sin `:`).

#### F8: CSS Mágico en Hover Helper
**Archivo**: `Components/NavTabs.vue:186-200`
**Problema**: `nth-child(2)`, `nth-child(3)`, etc. con offsets hardcodeados. Si se añade/elimina un tab, todo se desplaza.
**Riesgo**: Rotura silenciosa al modificar el menú.
**Fix**: Calcular posición del hover-helper dinámicamente.

---

### P3 — Bajo (nice to have)

#### F9: Código Comentado en Producción
**Archivo**: `AppLayout.vue:75-78` (transición de página), `AppLayout.vue:92` (PWANotifications), `navigation.js:359-373` (Blog submenu).
**Problema**: Múltiples bloques comentados que no se usan.
**Riesgo**: Ruido en codebase, el próximo developer pierde tiempo distinguiendo código vivo de muerto.
**Fix**: Limpiar o mover a rama de features.

#### F10: Ruta `archivos0`
**Archivo**: `navigation.js:422`
**Problema**: Nombre de ruta con sufijo `0`. Sugiere que hubo versiones anteriores.
**Riesgo**: Bajo, pero code smell de nomenclatura.
**Fix**: Renombrar a `archivos` si no hay conflicto.

---

## Patterns & Systemic Issues

### 1. El Menú como Sitemap
La navegación contiene entre 50 y 55 enlaces. Esto es sistémico — no es un error de implementación, es un error de arquitectura de información. La solución no es CSS ni código; es una decisión de contenido: ¿qué necesita ver el usuario en cada momento?

**Recomendación**: Evaluar con analytics qué enlaces reciben >5% de clicks. Mover el resto a:
- Footer navigation
- Secondary navigation (dentro de cada sección)
- Página de "Mapa del sitio" o "Índice"

### 2. Sin Breadcrumbs (Sistémico)
AppLayout.vue no renderiza breadcrumbs en ninguna página. Sin breadcrumbs, sin indicador de profundidad, sin "estás aquí". En sitios pequeños funciona, pero con 50+ páginas y jerarquía de 3-4 niveles es una omisión crítica.

**Recomendación**: Implementar breadcrumbs en el layout, visibles en todas las páginas excepto portada.

### 3. Solo un Punto de Entrada a la Navegación
Todo el contenido del sitio cuelga de 7 tabs. No hay navegación contextual (dentro de una página de "Filosofía", no hay nav lateral con los temas relacionados). El usuario depende exclusivamente del mega-menú.

**Recomendación**: Implementar navegación contextual dentro de cada sección (subnavigation vertical en páginas de categoría).

### 4. Sidebar como Ciudadano de Segunda Clase
NavAside es funcionalmente más débil que NavBar: sin hover, sin underscore, sin animaciones de entrada/salida, sin indicador de tab activo. Los usuarios mobile reciben una experiencia inferior.

**Recomendación**: Portar indicador de active tab a NavAside, añadir transiciones de acordeón, y considerar bottom nav para las 3-4 secciones principales en mobile.

---

## Positive Findings

1. **Arquitectura de store limpia**: `nav.js` separa concerns (state, getters, actions) y la función `mapItem/mapSubmenu` mantiene la lógica de transformación centralizada.

2. **Hover con delay inteligente**: 120ms de debounce evita falsos positivos. El hover-helper extiende el hit area, resolviendo el problema clásico de "submenú se cierra al mover el ratón en diagonal".

3. **Underline animado**: Priorización hover → current con transición CSS suave. Detalle de alta calidad que eleva la percepción del sitio.

4. **Escape key**: Implementación global que cierra submenús. Pequeño pero crucial para accesibilidad y power users.

5. **Descripciones en items**: Casi todos los items tienen `description`. Esto es excelente para comprensión y potencialmente para SEO/accessibility.

6. **Tema oscuro/claro**: Botón toggle bien implementado con aria-label y role="switch". Consistente en todo el layout.
