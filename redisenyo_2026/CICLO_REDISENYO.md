# Ciclo iterativo de rediseño con IA y navegación real

Objetivo: definir un flujo práctico para evaluar y mejorar la UI/UX del rediseño 2026 mediante navegación real, capturas visuales y revisión iterativa asistida por IA.

Decisión de herramientas:

- Priorizar herramientas gratuitas, locales o ya disponibles.
- En este proyecto sí se dispone de suscripción a Codex y OpenCode.
- No basar el ciclo inicial en servicios cloud de pago o herramientas caras.
- Mantener servicios externos como Browserbase, Percy, Chromatic o Applitools solo como opciones futuras, no como requisito del proceso.

## Idea principal

Es posible usar herramientas de automatización de navegador e IA para probar una web casi como lo haría una persona:

- abrir páginas reales;
- mover el flujo por enlaces y botones;
- cambiar viewport;
- capturar screenshots;
- revisar visualmente composición, jerarquía, legibilidad y usabilidad;
- aplicar cambios;
- repetir el ciclo.

Para TSEYOR, esto permitiría ejecutar ciclos de 10-20 iteraciones sobre la home, menú, curso, biblioteca, filosofía y quiénes somos, comparando cada versión contra los requisitos definidos en:

- `redisenyo_2026/BRANDING.md`
- `redisenyo_2026/REDISENO.md`
- `redisenyo_2026/MENU_REORGANIZACION.md`
- `redisenyo_2026/MEDICION_EXPERIMENTACION.md`

## Herramientas posibles

### Stack recomendado para este proyecto

Stack principal:

- Codex.
- OpenCode.
- Playwright.
- Screenshots locales.
- Checklist UX documentada en este archivo.

Este stack cubre la mayor parte del ciclo:

- edición de código;
- navegación real;
- capturas visuales;
- revisión responsive;
- evaluación con IA;
- iteración sobre componentes;
- validación reproducible.

No requiere contratar herramientas adicionales.

### Playwright

Playwright es la opción base más recomendable.

Permite:

- abrir Chromium, Firefox o WebKit;
- navegar rutas reales;
- hacer clic en botones y enlaces;
- rellenar formularios;
- simular desktop, tablet y móvil;
- capturar screenshots;
- hacer pruebas visuales con snapshots;
- detectar regresiones visuales.

Ventaja:

- Es determinista, reproducible y encaja bien en un proyecto de desarrollo.

Uso recomendado:

- Navegación funcional.
- Capturas visuales.
- Validación responsive.
- Detección de textos cortados, solapes, botones invisibles o menús rotos.

### Codex + Playwright

Codex puede combinar edición de código, ejecución de Playwright y revisión de capturas.

Flujo:

1. Codex lee los requisitos.
2. Ejecuta la web local.
3. Abre páginas con Playwright.
4. Captura screenshots.
5. Evalúa visualmente contra una checklist.
6. Edita componentes/CSS.
7. Repite.

Ventaja:

- Une implementación y evaluación visual en un mismo ciclo.

Uso recomendado para este proyecto:

> Codex + Playwright + screenshots + checklist UX + evaluación visual iterativa.

En este proyecto, Codex entra como herramienta disponible y prioritaria.

Responsabilidades recomendadas:

- leer y aplicar requisitos de `redisenyo_2026`;
- modificar componentes y estilos;
- ejecutar Playwright;
- revisar screenshots;
- iterar hasta cumplir criterios de cierre;
- dejar evidencia de cambios y validación.

### OpenCode + Playwright

OpenCode también puede formar parte del ciclo si se quiere contrastar propuestas o ejecutar una segunda revisión asistida.

Uso recomendado:

- revisión alternativa de screenshots;
- comparación de propuestas de layout;
- análisis de problemas de UX detectados por Playwright;
- segunda opinión antes de cerrar iteraciones importantes.

No hace falta que OpenCode sustituya a Codex. Puede funcionar como segunda capa de revisión cuando haya dudas de diseño o jerarquía.

### OpenAI Computer Use

OpenAI Computer Use permite que un modelo emita acciones de ordenador como clic, teclado o navegación, y reciba screenshots del estado resultante.

Ventaja:

- Se parece más a una persona manejando el mouse.
- Puede ser útil para tareas exploratorias donde no se conoce de antemano el selector o camino exacto.

Limitación:

- Es menos determinista que Playwright.
- Requiere más infraestructura.
- Para un rediseño controlado, puede ser excesivo como primera opción.

Uso recomendado:

- Escenarios exploratorios del tipo: `actúa como visitante nuevo y encuentra cómo inscribirte al curso`.
- Validación cualitativa adicional, no como base única del ciclo.

Estado para este proyecto:

- No usarlo como dependencia inicial.
- Considerarlo solo si Codex/OpenCode + Playwright no cubren algún caso exploratorio.

### Anthropic Computer Use

Anthropic Computer Use ofrece un enfoque parecido: un modelo puede controlar herramientas de ordenador y recibir el estado visual.

Ventaja:

- Útil para flujos de navegación real donde interesa comportamiento más humano.

Limitación:

- Igual que OpenAI Computer Use, añade complejidad frente a Playwright.

Uso recomendado:

- Auditorías exploratorias puntuales.
- No como primera capa de QA visual del proyecto.

### Browserbase Stagehand

Stagehand es un framework de automatización de navegador pensado para agentes IA.

Ventaja:

- Permite automatización más natural con instrucciones de alto nivel.
- Puede usarse localmente o con navegadores cloud de Browserbase.

Uso recomendado:

- Flujos donde interese una capa más semántica: `haz clic en el botón de inscripción`, `encuentra el acceso a biblioteca`, etc.
- Automatización con sesiones cloud si el proyecto crece en complejidad.

Estado para este proyecto:

- No prioritario.
- Descartado para el ciclo inicial si implica coste cloud o complejidad adicional.
- Puede reevaluarse en el futuro si se necesita navegación cloud o sesiones persistentes externas.

### Herramientas de visual regression

Ejemplos:

- Playwright visual snapshots.
- Percy.
- Chromatic.
- Applitools.

Ventaja:

- Detectan cambios visuales no deseados.
- Ayudan a mantener consistencia entre versiones.

Limitación:

- No sustituyen una evaluación UX. Detectan diferencias visuales, pero no siempre deciden si una versión comunica mejor.

Uso recomendado:

- Después de estabilizar el diseño base.
- Para evitar regresiones en hero, menú, tarjetas, responsive y formularios.

Estado para este proyecto:

- Usar primero Playwright visual snapshots, porque no añade coste externo.
- No usar Percy, Chromatic o Applitools en la fase inicial salvo que se decida explícitamente asumir el coste.

## Recomendación para TSEYOR

No empezar con Computer Use puro.

La opción más práctica es:

> Playwright como navegador real + Codex/OpenCode como agentes de implementación y evaluación + checklist UX basada en los documentos de rediseño.

Motivo:

- Es más simple de ejecutar.
- Es reproducible.
- Permite validar desktop, tablet y móvil.
- Encaja con el stack local.
- Reduce riesgo de decisiones erráticas.
- Permite iterar rápido sobre componentes reales.
- Aprovecha herramientas ya disponibles sin contratar servicios adicionales.

Computer Use o Stagehand pueden añadirse después para pruebas exploratorias, pero no son necesarios para comenzar.

## Herramientas descartadas para la fase inicial

No usar como base del ciclo inicial:

- Browserbase cloud.
- Percy.
- Chromatic.
- Applitools.
- Plataformas de heatmaps o session replay de pago.
- Sistemas A/B externos.
- Servicios cloud de navegación salvo necesidad clara.

Motivo:

- El objetivo actual no es montar una infraestructura compleja de experimentación.
- El rediseño necesita primero una versión base clara.
- El stack local ya permite navegación real, screenshots y evaluación visual.
- Añadir herramientas de pago ahora puede retrasar el trabajo principal.

Sí se permite:

- Playwright local.
- Capturas locales.
- Tests visuales con snapshots locales.
- Codex.
- OpenCode.
- Analítica GA4 ya existente.

## Ciclo propuesto de 20 iteraciones

Cada iteración debe tener un objetivo limitado.

Ejemplo:

1. Definir criterio de la iteración.
2. Ejecutar Playwright sobre rutas y viewports.
3. Capturar screenshots.
4. Evaluar contra checklist.
5. Identificar 1-3 problemas principales.
6. Aplicar cambios.
7. Repetir pruebas.
8. Registrar resultado.

No conviene cambiar todo en cada iteración. Mejor una mejora concreta por ciclo.

### Rutas mínimas a probar

- `/`
- `/cursos`
- `/biblioteca`
- `/filosofia`
- `/quienes-somos`
- `/blog`
- `/novedades`
- `/eventos`, si hay eventos programados.

### Viewports mínimos

Desktop:

- 1440 x 900
- 1280 x 800

Tablet:

- 768 x 1024
- 1024 x 768
- 900 x 1200
- 1194 x 834

Móvil:

- 390 x 844
- 430 x 932
- 360 x 800

Tablet es especialmente importante porque puede quedar como móvil ampliado o escritorio comprimido.

## Checklist de evaluación visual

### Primeros 5 segundos

- ¿Se entiende que TSEYOR no es una filosofía espiritual genérica?
- ¿Aparece el diferencial `filosofía de las estrellas`?
- ¿Se ve el CTA principal hacia el Curso Holístico?
- ¿Se ve una vía secundaria hacia Biblioteca?
- ¿La pantalla inicial inspira confianza y no saturación?

### Primeros 30 segundos

- ¿Queda claro que la filosofía proviene de los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia?
- ¿Aparece `sin dogmas` de forma visible y comprensible?
- ¿Se entiende que TSEYOR es una ONG sin ánimo de lucro?
- ¿Se entiende que los contenidos son libres o de libre descarga?
- ¿La persona sabe qué puede hacer después?

### Navegación

- ¿El menú prioriza Curso, Biblioteca, Filosofía, Quiénes somos y Blog?
- ¿Área miembros queda claramente separada?
- ¿El submenu se puede usar sin confusión?
- ¿El menú en tablet no queda apretado?
- ¿El drawer móvil prioriza los caminos públicos?

### CTAs

- ¿El CTA principal destaca sin parecer agresivo?
- ¿Los CTAs secundarios no compiten con el principal?
- ¿`Próximos eventos` aparece solo si tiene sentido?
- ¿Los botones en tablet no ocupan todo el ancho innecesariamente?
- ¿Los botones son fáciles de tocar en móvil?

### Responsive

- ¿No hay textos cortados?
- ¿No hay solapes entre imágenes, texto y botones?
- ¿Las tarjetas mantienen una altura razonable?
- ¿Las imágenes conservan significado?
- ¿La tablet se siente diseñada intencionalmente?

### Branding

- ¿La experiencia se siente luminosa, serena y acogedora?
- ¿El diseño evita parecer oscuro o sensacionalista?
- ¿Hay presencia humana suficiente?
- ¿El origen estelar se expresa con claridad y sobriedad?
- ¿La ONG y el carácter no lucrativo aparecen sin convertir la página en texto legal?

## Qué puede evaluar bien la IA

La IA con screenshots puede detectar bastante bien:

- CTAs poco visibles.
- Textos cortados.
- Solapes.
- Jerarquía visual débil.
- Botones demasiado grandes o pequeños.
- Menús confusos.
- Hero demasiado genérico.
- Falta de diferencial estelar en primer impacto.
- Secciones demasiado largas.
- Problemas de composición en tablet.
- Falta de contraste.

## Qué no debe decidir sola la IA

No conviene delegar por completo:

- tono espiritual final;
- sensibilidad del lenguaje sobre Guías Estelares;
- decisiones profundas de marca;
- estética fina;
- cambios grandes de narrativa;
- eliminación de contenidos relevantes;
- aprobación final de testimonios.

La IA puede señalar problemas y proponer mejoras, pero las decisiones finales de identidad deben quedar bajo criterio humano.

## Ejemplo de ciclo operativo

Iteración 1: Hero home.

- Abrir `/` en desktop, tablet y móvil.
- Capturar primer viewport.
- Evaluar si aparecen `filosofía de las estrellas`, CTA curso, biblioteca, ONG y sin dogmas.
- Corregir copy, tamaño de H1, posición de CTAs o imagen.
- Repetir capturas.

Iteración 2: Menú principal.

- Abrir menú en desktop.
- Abrir drawer en móvil.
- Probar rutas hacia Curso, Biblioteca, Filosofía, Blog y Área miembros.
- Evaluar si el orden refleja el rediseño.
- Ajustar etiquetas o submenús.

Iteración 3: Tablet.

- Probar 768 x 1024 y 1024 x 768.
- Revisar hero, CTAs, tarjetas y menú.
- Evitar botones full-width de tablet.
- Ajustar grids.

Iteración 4: Página Curso.

- Validar hero, inscripción, prueba social, sin dogmas y claridad de gratuidad.
- Probar clic hacia inscripción.

Iteración 5: Biblioteca.

- Validar libre descarga, comunicados, libros, audios y materiales.
- Comprobar que la página no abruma al usuario nuevo.

Repetir hasta cubrir páginas principales y estados responsive.

## Registro de iteraciones

Cada iteración debería registrar:

- fecha;
- rutas probadas;
- viewports probados;
- screenshots generados;
- problemas detectados;
- cambios aplicados;
- resultado;
- problemas pendientes.

Formato sugerido:

```md
## Iteración 03 - Tablet home

Rutas:
- `/`

Viewports:
- 768 x 1024
- 1024 x 768

Problemas:
- CTA principal demasiado ancho en tablet vertical.
- Tarjetas de caminos se sienten como móvil ampliado.

Cambios:
- CTAs pasan a ancho natural con `flex-wrap`.
- Tarjetas pasan a grid 2x2.

Resultado:
- Tablet se siente más intencional.
- Pendiente revisar menú en tablet horizontal.
```

## Criterios de cierre

El ciclo puede darse por suficientemente completo cuando:

- La home supera la prueba de 5 segundos y 30 segundos.
- El menú refleja el recorrido público y separa Área miembros.
- Curso, Biblioteca, Filosofía y Quiénes somos tienen CTAs claros.
- Tablet no parece ni móvil ampliado ni escritorio comprimido.
- No hay solapes ni textos cortados en viewports principales.
- Los screenshots finales son consistentes con branding.
- La navegación hacia Curso, Biblioteca, Guías Estelares, Blog y Eventos funciona.

## Fuentes y referencias

- Playwright visual comparisons: https://playwright.dev/docs/next/test-snapshots
- OpenAI Computer Use: https://platform.openai.com/docs/guides/tools-computer-use/
- Anthropic Computer Use: https://docs.anthropic.com/en/docs/agents-and-tools/tool-use/computer-use-tool
- Browserbase Stagehand: https://www.browserbase.com/stagehand/
