# Medición y experimentación ligera

Objetivo: definir cómo evaluar el rediseño de TSEYOR sin complicar demasiado la implementación ni introducir tests A/B prematuros.

## Punto de partida

La mayoría de usuarios actuales de la web parecen ser miembros o personas ya vinculadas a TSEYOR. Todavía no se ha identificado una afluencia clara de usuarios externos nuevos.

Esto afecta a cualquier análisis de conversión:

- Los miembros ya conocen la web y navegan con hábitos establecidos.
- Los miembros pueden buscar novedades, herramientas, equipos, salas, archivos o contenidos concretos.
- Un visitante nuevo necesita otro recorrido: entender qué es TSEYOR, confiar, explorar biblioteca o inscribirse al curso.
- Si mezclamos ambos perfiles sin distinguirlos, las métricas pueden llevar a conclusiones equivocadas.

## Por qué no hacer A/B testing formal ahora

No se recomienda implementar tests A/B formales en esta fase.

Motivos:

- Añade complejidad técnica y analítica.
- Requiere suficiente volumen de visitantes nuevos para que los resultados tengan sentido.
- Puede contaminarse si la mayoría de tráfico son miembros recurrentes.
- Obliga a gestionar variantes, persistencia, segmentación, consentimiento/cookies y análisis posterior.
- Puede retrasar el rediseño principal, que ahora necesita claridad estratégica más que optimización estadística fina.

Conclusión:

> Primero conviene implementar una versión base fuerte, coherente con branding y navegación. Después, medir de forma simple. Solo si hay volumen suficiente de visitantes no logueados tendría sentido plantear A/B testing.

## Enfoque recomendado

Usar experimentación ligera, no A/B formal.

Principios:

- No crear variantes automáticas de página en esta fase.
- No depender de cookies nuevas si no son necesarias.
- Aprovechar la sesión existente para distinguir usuarios logueados y no logueados.
- Medir eventos básicos de navegación y conversión.
- Revisar datos después de unas semanas antes de decidir cambios.

## Segmentación mínima útil

La distinción más simple y útil:

- Usuario logueado.
- Usuario no logueado.

Interpretación:

- Usuario logueado: probablemente miembro, colaborador o usuario recurrente.
- Usuario no logueado: puede ser visitante nuevo, persona externa, lector ocasional o miembro no autenticado.

Limitación:

No todos los no logueados son usuarios nuevos. Algunos miembros pueden navegar sin iniciar sesión. Aun así, esta segmentación es suficiente para una primera lectura sin complicar el sistema.

No se recomienda en esta fase:

- Crear cookies nuevas de visitante nuevo.
- Crear sistema de cohortes.
- Persistir variantes A/B.
- Añadir paneles complejos antes de saber qué decisiones se tomarán con esos datos.

## Métricas principales

La métrica principal del rediseño debe ser:

- Clic hacia `Curso Holístico`.
- Clic hacia `Inscríbete gratis`.

Estas métricas indican si la web está cumpliendo el objetivo de orientar hacia el curso.

Rutas asociadas:

- `/cursos`
- `/inscripcion`

## Métricas secundarias

Exploración:

- Clic hacia `Biblioteca`.
- Clic hacia `Comunicados`.
- Clic hacia `Libros`.
- Clic hacia `Audios`.

Diferencial estelar:

- Clic hacia `Guías Estelares`.
- Clic hacia `Filosofía`.
- Clic hacia `Orígenes de TSEYOR`.

Conexión humana:

- Clic hacia `Blog`.
- Clic hacia `Galerías`.
- Clic hacia `Experiencias`.

Retención:

- Clic hacia `Novedades`.
- Clic hacia `Boletines`.
- Suscripción al boletín.

Eventos:

- Clic hacia `Próximos eventos`.
- Clic hacia detalle de evento.

Confianza institucional:

- Clic hacia `Quiénes somos`.
- Clic hacia `ONG Mundo Armónico TSEYOR`.
- Clic hacia `Dónde estamos`.
- Clic hacia `Contactar`.

## Eventos recomendados

Eventos mínimos:

- `home_cta_curso_click`
- `home_cta_inscripcion_click`
- `home_cta_biblioteca_click`
- `home_cta_eventos_click`
- `home_guias_click`
- `home_blog_click`
- `menu_curso_click`
- `menu_biblioteca_click`
- `menu_filosofia_click`
- `menu_quienes_somos_click`
- `menu_blog_click`
- `menu_area_miembros_click`
- `boletin_suscripcion_submit`

Propiedades recomendadas:

- `url_actual`
- `destino`
- `texto_cta`
- `ubicacion`
- `usuario_logueado`
- `viewport_tipo`

Valores sugeridos para `ubicacion`:

- `hero`
- `franja_institucional`
- `caminos_home`
- `seccion_curso`
- `seccion_biblioteca`
- `menu_topbar`
- `menu_drawer`
- `footer`

Valores sugeridos para `viewport_tipo`:

- `mobile`
- `tablet`
- `desktop`

## Configuración recomendada en Google Analytics 4

La web ya cuenta con una base útil para GA4:

- `resources/js/composables/useGoogleAnalytics.js` carga `gtag.js`, registra pageviews en Inertia y permite enviar eventos personalizados.
- `HandleInertiaRequests.php` comparte `google_analytics.measurement_id` desde configuración.
- `config/services.php` lee `GOOGLE_ANALYTICS_MEASUREMENT_ID` y `GOOGLE_ANALYTICS_API_SECRET`.
- `AnalyticsController.php` ya puede enviar eventos vía servidor con Measurement Protocol para casos como `sendBeacon`.

Por tanto, no hace falta introducir una capa nueva de analítica. La recomendación es ordenar el uso de GA4 alrededor de eventos y parámetros estables.

### 1. Configuración base

Variables de entorno necesarias:

```env
GOOGLE_ANALYTICS_MEASUREMENT_ID=G-XXXXXXXXXX
GOOGLE_ANALYTICS_API_SECRET=xxxxxxxx
```

Uso recomendado:

- `GOOGLE_ANALYTICS_MEASUREMENT_ID`: necesario para cargar GA4 en frontend.
- `GOOGLE_ANALYTICS_API_SECRET`: necesario solo para eventos enviados desde servidor o `sendBeacon`.

No crear otra librería de tracking mientras `useGoogleAnalytics.js` siga funcionando. Añadir ahí los helpers nuevos para mantener una sola puerta de entrada a GA4.

### 2. Eventos: pocos nombres, parámetros claros

GA4 permite medir interacciones mediante eventos y añadir parámetros para describir mejor cada interacción. Para este rediseño conviene evitar decenas de eventos casi iguales.

En vez de usar un evento distinto para cada botón, se puede usar una mezcla:

- Eventos específicos para acciones realmente críticas.
- Eventos genéricos con parámetros para exploración y navegación.

Eventos específicos recomendados:

- `inscripcion_click`
- `curso_click`
- `boletin_suscripcion_submit`
- `contact_form_submit`

Eventos genéricos recomendados:

- `cta_click`
- `menu_click`
- `content_path_click`

Parámetros recomendados:

- `destino_tipo`: `curso`, `inscripcion`, `biblioteca`, `guias`, `blog`, `eventos`, `boletin`, `area_miembros`.
- `destino_url`: URL destino, solo si hace falta depurar. No usarla como dimensión principal.
- `ubicacion`: `hero`, `franja_institucional`, `caminos_home`, `seccion_curso`, `seccion_biblioteca`, `menu_topbar`, `menu_drawer`, `footer`.
- `texto_cta`: texto visible del enlace o botón.
- `usuario_estado`: `logueado` o `no_logueado`.
- `viewport_tipo`: `mobile`, `tablet`, `desktop`.
- `pagina_tipo`: `home`, `curso`, `biblioteca`, `filosofia`, `quienes_somos`, `blog`, `novedades`, `otro`.

Ejemplo conceptual:

```js
trackEvent('cta_click', {
  destino_tipo: 'curso',
  destino_url: '/cursos',
  ubicacion: 'hero',
  texto_cta: 'Comenzar el Curso Holístico',
  usuario_estado: page.props.auth?.user ? 'logueado' : 'no_logueado',
  viewport_tipo: getViewportTipo(),
  pagina_tipo: 'home'
})
```

Para el CTA de inscripción:

```js
trackEvent('inscripcion_click', {
  destino_tipo: 'inscripcion',
  destino_url: '/inscripcion',
  ubicacion: 'hero',
  texto_cta: 'Inscríbete gratis',
  usuario_estado: page.props.auth?.user ? 'logueado' : 'no_logueado',
  viewport_tipo: getViewportTipo(),
  pagina_tipo: 'home'
})
```

### 3. Dimensiones personalizadas en GA4

GA4 recoge eventos y parámetros, pero para analizar parámetros personalizados en informes y exploraciones hay que crear dimensiones personalizadas de alcance evento.

Crear en GA4:

Ruta orientativa:

`Administrar` -> `Visualización de datos` -> `Definiciones personalizadas` -> `Crear dimensión personalizada`

Dimensiones recomendadas:

| Nombre en GA4 | Alcance | Parámetro |
| --- | --- | --- |
| Destino tipo | Evento | `destino_tipo` |
| Ubicación CTA | Evento | `ubicacion` |
| Texto CTA | Evento | `texto_cta` |
| Estado usuario | Evento | `usuario_estado` |
| Tipo viewport | Evento | `viewport_tipo` |
| Tipo página | Evento | `pagina_tipo` |

No crear inicialmente dimensiones para:

- `url_actual`
- `destino_url`
- `page_location`
- `page_title`

Motivo: GA4 ya tiene dimensiones nativas para páginas, y las URLs completas pueden producir demasiados valores distintos. Para análisis de rediseño interesa más agrupar por intención: `curso`, `biblioteca`, `guias`, `blog`, etc.

### 4. Key events

En GA4, las acciones importantes deben marcarse como `key events`. No hace falta marcar todos los clics.

Marcar como key events:

- `inscripcion_click`
- `boletin_suscripcion_submit`
- `contact_form_submit`

Opcional:

- `curso_click`, si se quiere medir como microconversión.
- `home_cta_eventos_click`, si los eventos presenciales/online son un objetivo temporal relevante.

No marcar como key events:

- `menu_click`
- `cta_click` genérico.
- `page_view` global.
- `file_download` global.

Motivo: si se marca un evento demasiado amplio, la métrica deja de representar una acción realmente valiosa.

### 5. Exploraciones recomendadas

Crear 3 exploraciones simples en GA4.

Exploración 1: `Caminos de usuarios no logueados`

- Filtrar `usuario_estado = no_logueado`.
- Filas: `Destino tipo`.
- Columnas: `Ubicación CTA` o `Tipo viewport`.
- Métricas: eventos, usuarios, key events.

Objetivo: saber si los usuarios no logueados van hacia curso, biblioteca, guías, blog o eventos.

Exploración 2: `Embudo curso`

Pasos:

1. `page_view` en `/`
2. `curso_click` o `cta_click` con `destino_tipo = curso`
3. `page_view` en `/cursos`
4. `inscripcion_click`
5. `page_view` en `/inscripcion`

Segmentos:

- `usuario_estado = no_logueado`
- `usuario_estado = logueado`
- `viewport_tipo = mobile`
- `viewport_tipo = tablet`
- `viewport_tipo = desktop`

Objetivo: detectar si el usuario llega al curso pero no continúa hacia inscripción.

Exploración 3: `Menú principal`

- Evento: `menu_click`.
- Filas: `Destino tipo`.
- Columnas: `Estado usuario`.
- Métricas: eventos y usuarios.

Objetivo: comprobar si la reorganización del menú ayuda a visitantes nuevos sin perjudicar demasiado a miembros.

### 6. Informes mínimos

No crear un panel complejo al inicio.

Panel mínimo:

- Eventos por `destino_tipo`.
- Eventos por `usuario_estado`.
- Eventos por `viewport_tipo`.
- Key events por semana.
- Clics desde `hero` frente a `menu_topbar`.
- Clics hacia `curso` frente a `biblioteca`.

Lectura recomendada:

- Semana 1: verificar que los eventos llegan y que los parámetros no están vacíos.
- Semana 2-4: leer tendencias básicas.
- Después de 4 semanas: decidir ajustes de navegación o CTAs.

### 7. Debug y validación

Antes de dar la medición por válida:

- Usar el informe `En tiempo real` para comprobar que llegan eventos.
- Usar `DebugView` durante desarrollo si se activa modo debug.
- Comprobar que los eventos no se duplican en navegación Inertia.
- Comprobar que `usuario_estado` no envía datos personales.
- Comprobar que `viewport_tipo` clasifica correctamente tablet.
- Comprobar que `inscripcion_click` aparece antes de marcar conclusiones.

No tomar decisiones con datos de las primeras 24-48 horas si se acaban de crear dimensiones personalizadas. Es normal que tarden en estar disponibles para informes.

### 8. Privacidad y simplicidad

Para esta fase:

- No enviar nombre, email, nombre simbólico ni identificadores personales a GA4.
- No enviar ID interno de usuario.
- No crear cookies propias para detectar usuario nuevo.
- Usar solo `logueado` / `no_logueado`.
- Mantener los parámetros como categorías, no como datos personales.

Esto mantiene la medición suficiente para decisiones de UX sin convertir el rediseño en un proyecto de analítica complejo.

Fuentes oficiales útiles:

- Google Analytics Developers: configuración de eventos GA4 con `gtag.js` o Google Tag Manager: https://developers.google.com/analytics/devguides/collection/ga4/events?hl=es
- Google Analytics Help: dimensiones personalizadas de alcance evento: https://support.google.com/analytics/answer/14239696
- Google Analytics Help: dimensiones y métricas personalizadas: https://support.google.com/analytics/answer/14240153
- Google Analytics Help: key events: https://support.google.com/analytics/answer/13881540

## Lecturas útiles

Preguntas que se podrán responder sin A/B testing:

- ¿Los usuarios no logueados hacen clic en `Curso` o se van antes hacia `Biblioteca`?
- ¿El botón `Inscríbete gratis` genera más clics que `Ver curso`?
- ¿La Biblioteca funciona como segunda vía real de exploración?
- ¿`Guías Estelares` despierta interés cuando se muestra de forma visible?
- ¿El Blog ayuda a conectar o queda como ruta secundaria?
- ¿Los eventos generan clics cuando aparecen en portada?
- ¿Los usuarios logueados usan más `Área miembros` que las rutas públicas?
- ¿Tablet y móvil tienen patrones distintos de clic?

## Qué revisar después de implementar

Primer ciclo de revisión: 2-4 semanas después del rediseño.

Revisar:

- Clics totales por CTA.
- Clics por usuario logueado/no logueado.
- Clics por viewport.
- Secciones de home que generan más interacción.
- Menú: opciones más usadas y menos usadas.
- Eventos: si el CTA de eventos justifica su presencia en primer viewport.
- Biblioteca: si funciona como vía de exploración real.
- Curso: si el usuario llega al curso pero no continúa a inscripción.

## Señales para considerar A/B testing en el futuro

Plantear A/B testing solo si se cumplen varias condiciones:

- Hay volumen suficiente de usuarios no logueados.
- El tráfico externo aumenta de forma sostenida.
- Hay una hipótesis clara y limitada.
- La métrica de éxito está definida antes del test.
- El cambio a probar no afecta a muchas partes del sitio a la vez.
- Existe capacidad para analizar los resultados.

Ejemplos de tests futuros razonables:

- Hero A/B:
  - A: `Filosofía de las estrellas, conciencia sin dogmas`
  - B: `TSEYOR: filosofía estelar para el autodescubrimiento`

- CTA A/B:
  - A: `Comenzar el Curso Holístico`
  - B: `Inscríbete gratis`

- Orden de primera pantalla:
  - A: Hero -> sellos -> origen estelar -> caminos.
  - B: Hero -> origen estelar -> sellos -> caminos.

- Eventos:
  - A: `Próximos eventos` como botón junto a CTAs.
  - B: `Próximos eventos` como bloque contextual separado.

## Qué no medir todavía

Evitar métricas que parezcan sofisticadas pero no ayuden a decidir:

- Tiempo en página sin contexto.
- Scroll depth como métrica principal.
- Número bruto de páginas vistas mezclando miembros y visitantes nuevos.
- Tests con demasiadas variantes.
- Cambios de copy muy pequeños sin suficiente tráfico.
- Métricas agregadas sin separar usuario logueado/no logueado.

## Recomendación final

Implementar primero:

1. Rediseño base coherente.
2. Navegación reorganizada.
3. CTAs claros.
4. Medición básica de clics.
5. Segmentación simple por usuario logueado/no logueado.
6. Revisión a las 2-4 semanas.

No implementar A/B testing formal todavía.

La prioridad actual es reducir confusión, clarificar el diferencial de TSEYOR y hacer visibles los caminos principales: Curso, Biblioteca, Filosofía, Guías Estelares, Blog y Eventos.
