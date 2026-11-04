# Embudo de Captación de Alumnos — Estrategia

Estrategia y decisiones para aumentar el número de matriculados en el curso holístico, reduciendo la fricción del visitante interesado.

Documento de trabajo: recoge el análisis, el sopeso de opciones y las decisiones tomadas. Lo que queda pospuesto se lista al final.

---

## 1. Objetivo

Aumentar el número de matriculados al curso holístico **priorizando la calidad de los alumnos por encima de la cantidad**.

Criterio rector: no interesa maximizar formularios enviados, sino que lleguen al curso personas que realmente quieren formarse.

---

## 2. Contexto

### 2.1 Qué existe hoy

| Pieza | Estado |
|---|---|
| Portada `/` | Página larga de contenidos. Hero sin CTA de curso; el único enlace al curso está al final: en la FAQ (`Portada.vue:436`) y en el CTA final (`Portada.vue:485`) |
| Sección de boletín en portada | `Portada.vue:449-464` — ofrece el boletín general |
| Página del curso `/cursos` | CTA de inscripción en el hero (`Cursos/Index.vue:37`) y CTA final (`:367`) |
| Formulario `/inscripcion` | Público, con asignación de tutor posterior |
| Menú principal | Submenú «Curso → Inscríbete gratis» |
| Boletín general | Sistema propio: tabla `suscriptores`, preparación y envío programado. Sin terceros (Mailchimp/Brevo) |
| Miembros autenticados | **No ven la portada**: se les redirige a `/miembros` (`routes/web.php:154-156`) |

### 2.2 Por qué GA4 no sirve para analizar este embudo

Los datos históricos de analítica no son referencia válida: la mayoría de visitas provienen de miembros recurrentes del grupo o de la ONG que acuden a los contenidos que ya conocen. No representan a un visitante frío que llega por primera vez.

Además, hoy no hay conversión medida: el formulario de inscripción no dispara ningún evento y GA4 no tiene marcados eventos clave. **La medición del nuevo embudo deberá ser propia** (ver §7), no heredada.

---

## 3. Diagnóstico: las tres caídas del embudo

El recorrido desde que alguien aterriza hasta que se matricula tiene tres puntos de pérdida distintos. Cada uno necesita una palanca distinta.

| Caída | Problema |
|---|---|
| **A — No llega** | El curso sólo es visible al final de la portada. Quien no llega a hacer scroll, o no encuentra el enlace, nunca llega a `/cursos` |
| **B — No se atreve** | Llega a `/cursos` o incluso al formulario, pero el curso implica compromiso y esfuerzo, y se marcha sin inscribirse. Hoy no existe ningún escalón intermedio de compromiso cero |
| **C — Se cae después** | Se inscribe y luego, al contactarle el tutor, dice que no le interesa o que solo quería información. Se pierde por completo y, además, el tutor gasta energía en alguien desinteresado |

---

## 4. Sopeso de las tres palancas

| Palanca | Caída que resuelve | ¿Sube matriculados? | Ventana | Coste |
|---|---|---|---|---|
| **1. Hacer visible el curso sin recorrido agotador** | A | Sí, directo e inmediato | Días | Muy bajo |
| **2. Introducción por correo de bajo compromiso** | B | Sí, con retardo (semanas) | Media | Medio |
| **3. Secuencia automática al inscribirse** | C | No el recuento de formularios: sube *alumnos activos* y eficiencia del tutor | Inmediato | Medio |

**Conclusión del sopeso:** con el criterio *calidad por encima de cantidad*, las palancas **2 y 3 son las que más encajan**, porque filtran y cualifican. La palanca 1 es táctica: no persigue persuadir, sino que el curso sea visible para quien ya está preparado.

Las palancas 2 y 3 **comparten el mismo contenido**: una única secuencia de introducción con dos disparadores distintos. No se redacta dos veces.

---

## 5. Decisiones tomadas

### 5.1 Portada

1. **La portada sigue siendo de contenidos primero.** Su objetivo es que en pocos pasos se entienda qué es Tseyor; el curso sólo si lo visto resulta interesante.
2. **No se reinstala el CTA del curso en el hero.** Se decidió retirarlo porque invitar a un curso «de algo» sin saber todavía qué es ese algo es pedir en la etapa de decisión a alguien que aún está en etapa de comprensión. Se mantiene retirado.
3. **La sección que hoy ofrece el boletín general (`Portada.vue:449-464`) pasa a ofrecer la introducción a Tseyor por correo** (secuencia de 7 correos).
   - Aprovecha un espacio ya existente, sin agobiar ni añadir bloques nuevos.
   - Fricción mínima: no exige compromiso, solo recibir información.
   - El visitante puede seguir scrolleando y encontrar después el CTA del curso.
4. **Consecuencia aceptada:** el boletín general deja de ofrecerse en la portada. Queda en el pie del resto de páginas (`AppFooter`) y se ofrece como segundo nivel al terminar la secuencia de introducción.

Estructura resultante de la portada:

```
Hero      → entender qué es Tseyor            (sin pedir nada)
Medio     → introducción por correo (7 correos) ← antes: boletín general
Final     → CTA del curso                      (ya saben qué es)
Pie       → boletín general (nivel 2)          (solo fuera de la portada)
```

### 5.2 Secuencia de introducción

5. **Una sola secuencia de 7 correos, dos disparadores:**
   - **Alta automática** al enviar el formulario de inscripción, si la persona no estaba ya dada de alta.
   - **Alta voluntaria** desde la portada y demás puntos de captación.
6. **La secuencia arranca durante la espera entre la inscripción y el contacto del tutor.** Ese lapso va de 2-3 días a 1 semana, y es variable.
7. **La secuencia no se ata a la ventana de espera.** Los primeros correos ocurren durante la espera; los restantes continúan después. El tiempo que el tutor no ha logrado cerrar —o el momento en que la persona descarta el curso— es precisamente el mejor instante para seguir hablándole.
8. **Cancelación en cualquier momento**, con enlace de baja en cada correo (ya existe infraestructura de baja por token).
9. **Objetivo de filtro:** la secuencia detecta desinterés. Quien se da de baja o no abre nada se descarta, y la energía de los tutores se concentra en quienes siguen interesados.

### 5.3 Puntos de captación

10. **Portada:** sección de introducción (§5.1). Es la ubicación de menor fricción posible.
11. **Página del curso `/cursos`:** la introducción se ofrece como opción de compromiso cero para quien llega hasta ahí dudando.
12. **Formulario de inscripción:** la introducción puede ofrecerse **solo como opción secundaria, por debajo del botón de envío y como enlace de texto** — nunca por encima ni con el mismo peso que la inscripción, para no competir con el CTA principal.

---

## 6. Decisiones rechazadas

| Propuesta | Motivo del rechazo |
|---|---|
| CTA del curso en el hero de la portada | Mismatch de etapa: se pide decisión a quien aún está comprendiendo qué es Tseyor |
| Oferta de la introducción como CTA principal dentro del formulario de inscripción | Competiría con el propio botón de inscripción. Solo es admisible como salida secundaria |
| Popup de entrada (aparecer nada de cargar) | Intrusivo; Google penaliza los *interstitials* a pantalla completa en móvil |
| Basarse en GA4 histórico para decidir dónde colocar | Audiencia mayoritariamente recurrente, no representa al visitante frío |
| Crear una página nueva como ubicación de captación | Una página sin tráfico propio no convierte por sí sola |
| Copy que filtra por la herida («¿nunca has sentido que no eres del todo de este mundo?») | Suena a atrapa-incautos: explota a quien está desdichado, desplazado o en debilidad social. Además contradice «sin líderes, sin atajos»: prometer pertenencia a quien no la tiene es postura de líder salvador. El filtro va por **aspiración**, no por carencia |

---

## 7. Medición del embudo

Con el criterio de que la analítica existente no es referencia, la medición debe ser propia y sencilla:

| Qué medir | Cómo |
|---|---|
| **Altas y su procedencia** | Campo de origen en el alta: `portada`, `cursos`, `formulario`, etc. |
| **Progreso en la secuencia** | Paso alcanzado por cada suscriptor y fecha de cada envío |
| **Interés real** | **Clics con enlace con token** en cada correo — más fiable que las aperturas, que los clientes de correo las inflan artificialmente |
| **Conversión final** | Origen registrado en el formulario de inscripción, para cerrar el bucle |
| **Filtrado** | Altas que se dan de baja antes de completar la secuencia |

KPIs del embudo: **altas → completan la secuencia → clic en el correo final → inscripción al curso.**

Con el origen registrado en cada punto de captación, la comparación entre ubicaciones se decide con datos propios semanas después de implantar, sin depender de supuestos.

Google Analytics queda como complemento, no como referencia: conviene marcar los eventos clave que hoy figuran como no configurados.

---

## 8. Pendientes y pospuestos

| Pendiente | Nota |
|---|---|
| **Matizar la respuesta de la FAQ «¿Cómo puedo empezar?»** (`Portada.vue:436`) | Hoy dice «la puerta de entrada recomendada es el curso holístico». Aclarar que la puerta de entrada es acceder a la formación necesaria para ser miembro del grupo/ONG, no el curso en sí |
| **Contenido de los 7 correos** | Última prioridad; se decide cuando el resto del embudo esté claro |
| **Rediseño interno** (esquema de datos, alta de suscriptores, sistema de envío) | Pospuesto expresamente: primero se cierra la estrategia |
| **Efectividad por ubicación** | Se resuelve con la medición de origen (§7), una vez implantado |

---

## 9. Siguientes pasos

1. Rediseñar la sección de portada: boletín general → introducción por correo.
2. Definir la medición de origen antes de abrir la captación.
3. Decidir las ubicaciones restantes (`/cursos`, formulario) sobre esa base.
4. Redactar la secuencia de 7 correos.
5. Abordar el rediseño interno que sustente la secuencia.

---

## 10. Cierre de decisiones — copy de entrada (entrada fría)

| Decisión | Detalle |
|---|---|
| **Objetivo de la secuencia** | Que la persona responda **«¿esto es para mí?»**. Revelar y filtrar, no convencer: las dos salidas («es lo que buscaba» / «no es para mí») son aciertos. |
| **Alcance de esta fase** | Entrada fría desde portada y puntos de captación. La persona **puede no saber que existe el curso**: el copy no lo menciona, ni el tutor. La modalidad *alta automática al inscribirse al curso* se trata aparte, más adelante. |
| **Cifra de libros** | **«Más de 400 libros»** es correcta en producción. La base local es beta y no está sincronizada (sitemap local: 393). |
| **Eje de filtrado del copy** | Por **aspiración y curiosidad** —las preguntas propias del proyecto («¿quién soy?, ¿a dónde voy?, ¿de dónde vengo?, ¿cuál es el propósito de mi vida?»)— y nunca por **carencia** (§6). |
| **Copy con más recorrido** | «Tu vida puede tener un sentido mucho más profundo» encaja con los lemas del proyecto: es la preferida hasta la fecha. |
| **Cadencia** | **CERRADA: 1 correo al día** (decisión del proyecto). Premisa del *ritmo ≠ carga*: las lecturas son sugerencias opcionales —≈3-5 min, cero tareas—, así que el ritmo diario se sostiene. Los cierres *«Mañana:»* de los correos 1-6 son correctos. Una guía de lectura propiamente dicha sería otra secuencia aparte. |
| **Copy elegido** | Titular: **«7 correos para conocer qué es Tseyor»**. Subtítulo = sumario honesto de la secuencia (Guías Estelares → su propósito y mensaje → cómo ayudar → Sociedades Armónicas). CTA: «Quiero conocerlo». Montado en la portada. |
| **Oferta final (correo 7)** | Tras entregar valor, el correo 7 invita a ser miembro de Tseyor y a participar en «esta aventura cósmica» ofreciendo **el curso holístico gratuito**, con requisitos, condiciones y garantías, o enlace a `/cursos`. |
| **Enseñanza a incluir** | En al menos un correo: **lo más importante es trabajar en el pensamiento** — ahí está todo. |

### 10.1 Discrepancia detectada: nombre del curso — RESUELTA

El correo 7 debe nombrar el curso. En la página pública `/cursos` el nombre visible es **«Curso Holístico Tseyor»** (`Cursos/Index.vue:14,171`) y se declara **100% gratuito** (`:22,:360,:363,:367`).

**Resolución aplicada (opción c):** se añadió **«Las 12 esferas del universo»** como subtítulo dentro del `<h1>` y del `<h2>` del libro principal de `/cursos` (`Cursos/Index.vue:16,176`), de modo que el correo y la landing dicen lo mismo en la primera mención. Verificado en build.

**Bonus:** la cadena no es inventada del proyecto: es material de la casa — los comunicados **59** (*«Cuento de Christian y las 12 esferas del universo»*, TAP, 2007) y **388** (*«Tercer acto del cuento de Christian y las 12 esferas»*, GEN, 2001) (`resources/data/comunicados.lst:407,1283`).

---

## 11. Esquema de la secuencia (borrador v2)

**Principio de diseño:** cada correo enseña **una parte distinta de la web** — un libro (1), páginas de filosofía (2, 3), el glosario (3, 4), la propia ONG y sus centros (5), la visión (6). La secuencia es a la vez introducción y recorrido guiado por el sitio.
Los correos 1-6 **no mencionan el curso**; el correo 7 es la única oferta.

| # | Tema | Ancla en la web | Notas |
|---|---|---|---|
| 1 | **Los Guías Estelares** | Páginas de tres guías: `/guias/shilcars`, `/guias/noiwanak`, `/guias/aium-om` | **Gancho de filtro**: si no le interesa o le parece falsa, se descarta solo. **No se enlaza el libro «Los Guías Estelares»**: es material del curso, denso para un primer contacto; quien quiera más lo encontrará en la propia página de Shilcars. Ver §11.4 |
| 2 | **Los tiempos de cambio** | Página «El Rayo Sincronizador» (`https://tseyor.org/el-rayo-sincronizador`) | Con citas breves del propio material |
| 3 | **El pensamiento** | Términos del **glosario** | Meditación e interiorización, la alquimia de la sublimación del pensamiento; y para qué: ego, agregados psicológicos. Enlaza uno o varios términos del glosario para darlo a conocer |
| 4 | **Cómo se vive en el día a día** | Términos del **glosario** | Humildad y bondad **como práctica**, no como catálogo de virtudes. Responde *¿qué se espera de mí?*, que era la fila vacía de §11.3 |
| 5 | **TSEYOR, lo que hacemos** | `/centros`, `/quienes-somos` | La ONG y sus centros reales. **Absorbe el contactismo ampliado**: avistamientos, abducciones, relaciones de más de 40 años con seres humanos vivos de otros lugares del cosmos, y **cómo juzgar su autenticidad** (ver §11.4) |
| 6 | **Los orígenes** | Libro de la biografía de Chac-Mool Puente *(próximo en la web)* | Narrativa: cómo empezó todo, con una historia de Puente. **Sustituye a «Sociedades Armónicas»**, que se resolvía flojo como correo independiente |
| 7 | **La invitación** | `/cursos` | Ser miembro y «participar en esta aventura cósmica»: el **curso holístico gratuito** («Las 12 esferas del universo») con requisitos, condiciones y garantías. **Mención breve a las Sociedades Armónicas** como destino: ahí consiste todo esto |

**Gratuidad:** se dice en **dos** sitios y solo en dos: una línea en el correo 1 (corta la duda «¿y esto qué me quiere cobrar?» antes de que nazca) y el cierre del correo 7 (la oferta). No se repite en el medio.

#### Guion de apartados

Reglas comunes a los siete: **un solo enlace principal por correo**, cierre anunciando el siguiente, enlace de baja en todos, y **sin mencionar el curso hasta el 7**.

**1 — Los Guías Estelares**
1. Gancho de filtro: *«No nos creáis, comprobadlo»* — no se pide credulidad, se entrega material para juzgar.
2. Mención breve del contactismo: contactamos con seres humanos vivos de otros lugar del cosmos; avistamientos y abducciones; relaciones de más de 40 años. Sin desarrollar: se amplía más adelante.
3. Los tres guías: Shilcars, Noiwanak y Aium Om → `/guias/shilcars`, `/guias/noiwanak`, `/guias/aium-om`.
4. Una línea de gratuidad.
5. Cierre: qué viene.

**2 — Los tiempos de cambio**
1. Los tiempos de cambio en los que estamos, con citas breves.
2. Término sensible a la primera mención: **Cristo Cósmico** + glosa (chispa divina interior).
3. Bloque **«¿Es Tseyor una religión?»** — texto aprobado (§11.2).
4. Ampliación: enlace a **El Rayo Sincronizador**.
5. Cierre: qué viene.

**3 — El pensamiento**
1. La meditación y los trabajos de interiorización.
2. El porqué: trabajar el pensamiento, la revolución del pensamiento, la alquimia de la sublimación. *«Lo más importante es trabajar el pensamiento: ahí está todo.»*
3. ¿Para qué sirve?: ego y agregados psicológicos.
4. Ancla: uno o varios términos del **glosario**.
5. Cierre: qué viene.

**4 — Cómo se vive en el día a día**
1. Humildad y bondad **como práctica**, no como catálogo de virtudes.
2. Responde *¿qué se espera de mí?*, la fila que estaba vacía (§11.3).
3. Ancla: **glosario**.
4. Cierre: qué viene.

**5 — TSEYOR, lo que hacemos** *(pieza de credibilidad)*
1. La ONG y sus centros: `/centros`, `/quienes-somos`.
2. El contactismo ampliado: avistamientos, abducciones, relaciones de más de 40 años con seres humanos vivos de otros lugares del cosmos.
3. Cómo se juzga: autenticidad, pruebas y fotografías, contradicciones (material legado).
4. Cierre: *«No nos creáis, comprobadlo.»*

**6 — Los orígenes**
1. Cómo empezó todo.
2. Una historia de **Chac-Mool Puente**, el canalizador: anécdota corta sacada del libro.
3. Ancla: el **libro de la biografía** (historias de 2-3 páginas, próximamente en la web).
4. Cierre: qué viene.

**7 — La invitación**
1. Ser miembro de Tseyor y «participar en esta aventura cósmica».
2. El **curso holístico gratuito**: «Las 12 esferas del universo».
3. Requisitos, condiciones y garantías, o enlace a `/cursos`.
4. Mención breve a las **Sociedades Armónicas** como destino: ahí consiste todo esto.
5. Gratuidad, segunda y última mención.
6. Sin compromiso y baja en un clic.

**Nota sobre el subtítulo de portada.** Sigue vigente sin cambios: *«Cómo puedes ayudar y cómo llegar a vivir en las Sociedades Armónicas»* se cumple en el **correo 7** — ayudar, participando; Sociedades Armónicas, como destino. El subtítulo promete contenidos, no un índice correo a correo.

**Dependencia del correo 6.** El libro de la biografía **aún no está en la web**. El correo 6 no puede enviarse hasta que el libro esté publicado, igual que el correo 2 depende de que `https://tseyor.org/el-rayo-sincronizador` siga viva (§11.1). Conviene cerrar el slug del libro antes de componer el enlace.

### 11.1 Enlaces: producción frente a beta

Los suscriptores son personas reales en **producción**, así que los enlaces de los correos deben apuntar a URLs de producción. Tres desajustes que conviene no perder de vista:

- **`/el-rayo-sincronizador` no existe en este repositorio**: en esta versión esa sección pasó a `/filosofia` (+ `/filosofia/temas`). Cuando esta versión sustituya a producción, **todos los enlaces ya enviados empezarían a dar 404** si no se crean las redirecciones correspondientes. Es un **requisito de migración**.
- El libro «Los Guías Estelares» no está en la base local (beta sin sincronizar).
- El libro del curso, aquí, se sirve en `/libros/curso-holistico-tseyor`. Si en producción el slug es distinto, alinear.

Ritmo de envío **cerrado: 1 correo al día** (§10); el gancho del correo 1 y el contenido del correo 4 también están resueltos (§12.1, §12.4). Antes de enviar solo quedan dos cosas: **slug y publicación del libro** (correo 6) y **confirmación de «participar en esta aventura cósmica»** (correo 7, §12.7).

### 11.2 Términos sensibles y la objeción «¿es una religión?»

**Decisión:** el bloque va en el **correo 2**, donde aparece por primera vez un término sensible («Cristo Cósmico»), y no antes. Responder la objeción **en el momento en que nace** es más persuasivo que anticiparla: levantar el tema sin que nadie lo haya planteado siembra la duda en vez de disiparla.

**Qué no se dice:** la palabra «secta». No la menciona nadie, y nombrarla la instala. Se desactiva sin decirla con los claims verificados de la propia web: *«no hay dogmas, no hay credo que suscribir»* (`Portada.vue:418`), *«sin dogmas, sin líderes, sin atajos»* (hero) y *«puedes ser ateo, agnóstico, espiritual o religioso»* (`Portada.vue:418`).

**FAQ de portada — sustituida y luego acortada.** La web **no respondía** a «¿es Tseyor una religión?»: solo cubría la pregunta vecina *«¿Hay que tener alguna creencia para participar?»* (`Portada.vue:414-418`), que el propio proyecto consideraba pobre. Sustituida por **«¿Es Tseyor una religión?»**. Se le añadió una glosa de los términos sensibles («no hace falta creerlos para participar») que **se retiró después por longitud**: la duda de fe queda cubierta mejor por *«No nos creáis, comprobadlo»* (§11.4), en la voz de los propios guías, y sin alargar la respuesta. **No se añaden más preguntas a la FAQ**: el proyecto prefiere la frase en la web a otra entrada en el listado.

**Texto aprobado (v2, el que está en producción):**

> No. No hay dogmas, ni credo que suscribir, ni líder al que obedecer: puedes ser ateo, agnóstico, espiritual o religioso. Respetamos todas las religiones porque la verdad no es propiedad de nadie: está en lo más profundo de cada uno. En Tseyor llamamos *religare* a ese encuentro con nuestra naturaleza espiritual, cuántica y divina.

**Patrón de término sensible:** *glosa a la primera mención*, con las palabras que el sitio ya usa, no con otras nuevas. «Cristo Cósmico» ya está glosado en la web: *«nuestra naturaleza profunda, nuestra chispa divina o Cristo Cósmico interior»* (`Filosofia.vue:195`). Texto propuesto para el correo 2:

> Cuando hablamos del **Cristo Cósmico** no nos referimos a una figura histórica ni a una devoción: es el nombre que damos a esa chispa divina que hay en el interior de cada ser.

**Correo 1 y «Cristo Cósmico» — cerrado.** El término aparece también en el libro «Los Guías Estelares», pero **no se le da tratamiento especial en el correo 1**: quien abre ese libro ya ha entrado en materia, ha leído antes otros textos del mismo libro que le parecieron interesantes y tiene buena predisposición. El desarrollo completo va en el correo 2.

### 11.3 Cobertura de las preguntas clave del visitante frío

Inventario del material legado de FAQ (`resources/preguntas/preguntas{1,2,3}.html`): **374 preguntas numeradas** en 18 clusters. Los clusters mayoritarios son *cambio de eras, preparación y rayo sincronizador* (preguntas2) y *curso, símbolos y nave* (preguntas3); la propia organización ocupa poco.

| Pregunta del visitante frío | Dónde se responde hoy |
|---|---|
| ¿Qué es esto? | Correos 1 (parcial) y 5 |
| ¿Quiénes son los guías? ¿Es comprobable? | Correo 1 |
| ¿Es una secta? | Legado `preguntas3.html:1065` y FAQ de portada (implícito) |
| ¿Es una religión? | **FAQ de portada (nueva)** y correo 2 |
| ¿Tengo que creer en algo? | Correo 2 (glosa de términos) |
| ¿Para qué ahora? | Correo 2 |
| ¿Qué hago yo conmigo? | Correo 3 |
| ¿Cómo se vive? ¿Qué se espera de mí? | Correo 4 *(pendiente de reconvertirlo: ver abajo)* |
| ¿Quién está detrás? ¿Es real? | Correo 5 |
| ¿Hacia dónde vamos? ¿Qué gano? | Correo 7, como destino de lo ofrecido |
| ¿Y ahora qué hago? | Correo 7 |
| **¿Cuánto cuesta?** | **Correos 1 y 7** — una frase de gratuidad en cada uno |
| **¿Qué me van a pedir en serio?** | **Ningún correo** *(materia prima en `preguntas3.html:1065`, no asignada)* |
| **¿Quién lo fundó? ¿De dónde sale esto?** | **Correo 6** — *Los orígenes*, con anécdota de Chac-Mool Puente y enlace al libro de la biografía *(pendiente de que el libro esté publicado)* |
| **¿Y mi familia si no lo compra?** | **Ninguno** |

**Tres huecos, estado tras las decisiones tomadas.** (1) **Dinero y compromiso**: la *gratuidad* ya está cubierta — una frase en el **correo 1** y otra en el **correo 7** (§11.4). Sigue abierto el flanco del **compromiso en tiempo y dedicación** («¿qué me van a pedir?»): el material legado sí tiene materia prima en `preguntas3.html:1065` y conviene asignarlo al **correo 5**, el de «lo que hacemos». (2) **Origen y fundación**: cubierto por el **correo 6**, *Los orígenes*, con anécdota de Puente y enlace al libro de la biografía; queda pendiente de que el libro esté publicado. (3) **El entorno familiar**, hueco menor y de tratamiento delicado, sigue sin asignar a ningún correo.

**Correo 4, el único sin trabajo asignado.** «Humildad y bondad» es contenido que podría firmar cualquier grupo y no responde ninguna de las preguntas de la tabla. Recomendación: darle oficio convirtiéndolo en **«cómo se vive en el día a día»** — la humildad y la bondad como práctica, no como catálogo de virtudes—, que es exactamente la fila que hoy está vacía (*¿qué se espera de mí?*).

**Dato a favor del texto de religión:** la fórmula *«religare íntimo»* ya aparece en el material legado (`preguntas3.html:1071`), dentro de la respuesta a *«¿Es esto una secta?»* (`:1065`). La propuesta de copy no inventa voz nueva: recupera la voz de la casa.

### 11.4 La pregunta capital: «¿es verdadero?»

La duda que el propio proyecto recuerda como la más urgente al acercarse no es doctrinal, sino de **autenticidad**: *¿he encontrado una fuente verdadera o es un timo?*, e incluso —dentro de la misma duda— *¿y si la canalización es real pero los seres que hablan no son benevolentes?*

**Ningún correo la responde hoy.** Y es la última barrera antes de la invitación: contento puede estar el lector, pero si no resuelve «¿esto es real?», no da el paso.

Dos decisiones de tratamiento:

1. **No se nombra la hipótesis del ser malévolo.** Mismo criterio que con «secta»: nadie la pronuncia en voz alta y nombrarla la instala. Se desactiva respondiendo a la clase de duda —*cómo juzgar la autenticidad*— y no a la imagen concreta.
2. **Se responde con criterio, no con afirmación.** El material legado ya tiene la materia prima: *«¿Cómo puedo estar seguro de que el contacto es auténtico?»* (`preguntas1.html:1488`), *«¿Qué hay de las pruebas o fotografías de avistamientos?»* (`:1635`), *«¿Por qué a veces dicen cosas contradictorias?»* (`:2050`).

**Correo 1, estructura acordada:**

1. **Gancho de filtro** — no se pide credulidad: se entrega material para juzgar.
2. **Mención breve del contactismo** — este grupo contacta con seres humanos vivos de otros lugares del cosmos; ha habido avistamientos y abducciones; se mantienen relaciones desde hace 40 años o más. Basta una mención: introduce el tema, **promete continuación en un correo futuro** y abre la puerta a la duda de autenticidad.
3. **Los tres guías** — Shilcars, Noiwanak y Aium Om, con enlace a sus páginas.
4. **Una línea de gratuidad** — sin cuotas ni pagos ocultos.
5. **Cierre** — qué viene en el correo siguiente.

**Corrección 1 y 7 (y solo ahí):** la gratuidad se dice en dos sitios — una línea en el correo 1, que corta la duda «¿y esto qué me quiere cobrar?» antes de que nazca, y el cierre del correo 7, la oferta.

**La respuesta de la casa.** El material legado no es lo único que responde a la duda de autenticidad: los propios Guías Estelares lo han dicho, y es la frase que sostiene toda la secuencia:

> *«No nos creáis, comprobadlo.»*
>
> *«No nos creáis, experimentadlo, buscad la propia comprobación mediante la experimentación. Solo eso os conducirá hacia el camino de la objetividad. La creencia no sirve de nada, ni a favor ni en contra: es solo una creencia. Pero la experimentación es la propia comprobación y el verdadero guía.»*

Casi literal (transcripción de memoria, no cita textual verificada). **Es la respuesta mejor que puede haber**: no pide fe, la sustituye por comprobación. En términos de persuasión, traslada la autoridad al criterio del lector, que es exactamente lo que un escéptico necesita oír —y es coherente con el criterio de filtro de la secuencia (§10) y con el hero («sin dogmas, sin líderes, sin atajos»).

**Uso:** abre el **correo 1** como gancho de filtro y cierra el **correo 5** como respuesta a «¿es verdad?». Libro de la secuencia.

**En la web:** la frase ya está publicada en la portada, como cita destacada de la sección *«Las enseñanzas que nos transmiten»* (`Portada.vue:118-124`), usando el estilo de caja resaltada que ya emplea `/filosofia`. No se añadió pregunta a la FAQ: el proyecto prefiere la frase en la web a otra entrada más en el listado.

**Decisión: opción B.** Se mantienen **7 correos** y el titular de portada. El contactismo ampliado y la respuesta de autenticidad van dentro del **correo 5**, que pasa a ser la pieza de credibilidad institucional: quiénes somos, qué tenemos, con quién hablamos desde hace 40 años y por qué se puede comprobar.

---

## 12. Borradores de correo

### 12.1 Correo 1 — Los Guías Estelares

**Los borradores viven ahora en [`correos/`](correos/)**, un archivo por correo, para poder editarlos directamente: [`correos/correo1.md`](correos/correo1.md). Aquí se conserva solo el contexto de redacción; el texto que se envía es el del archivo.

**Material de origen:** la página `/guias` (`Guias/Index.vue:21-43`) y la plantilla de guía (`Guias/Guia.vue:50-101`). De ahí salen el tutelaje de la Confederación, la telepatía vía el canalizador, el dato de que la mayoría vive en otros planetas y algunos en bases en este planeta, y la bibliografía propia de cada guía.

**Confirmado:** el libro «Los Guías Estelares» **ya se enlaza desde las propias páginas de los guías** (`Guias/Index.vue:7`, `Guias/Guia.vue:7`, enlace a `/libros/los-guias-estelares`). El lector interesado lo encuentra sin que el correo lo proponga: la decisión de no enlazarlo en el correo 1 no lo esconde, solo evita la densidad.

**Notas de redacción:** los cinco apartados acordados en §11.4 se mantienen; la ampliación añade dos bloques («¿De qué hablamos?» y «¿Cómo contactan?») y sustituye la promesa genérica de «quién es y qué dice» por el detalle real de cada página de guía. El gancho sigue siendo la frase de los guías, no una promesa. El *Rayo Sincronizador* queda sembrado como objetivo del tutelaje y se desarrolla en el correo 2. Un solo bloque de enlaces (los tres guías). La gratuidad ocupa una frase y desaparece. El cierre abre el correo 2.

### 12.2 Correo 2 — Los tiempos de cambio

Borrador: [`correos/correo2.md`](correos/correo2.md). Apartados del guion §11: tiempos de cambio con citas breves → glosa del *Cristo Cósmico* → bloque *¿es una religión?* (texto aprobado §11.2) → enlace a *El Rayo Sincronizador* → cierre al correo 3.

**Material de origen:**

- Mensaje de cambio y despertar + «comunicados documentados públicamente desde 1988» + «del individuo a la comunidad, del miedo al amor, de la separación a la unidad»: `Portada.vue:79, :82, :116`.
- Paréntesis de realidad limitada, despertar como recuperación de la naturaleza profunda y glosa del Cristo Cósmico: `Filosofia.vue:186-196`.
- Resituar a la humanidad en su posición real (Tiempo Real): `Guias/Index.vue:30`.
- Método de la casa («conocer, experimentar, contrastar y obtener las propias conclusiones»): `Filosofia.vue:257-259` — coherente con «no nos creáis» del correo 1.

**Dependencia:** el único enlace principal es `https://tseyor.org/el-rayo-sincronizador`, que **existe en producción** pero no en esta beta (pasó a `/filosofia`). Requisito de migración ya anotado en §11.1.

### 12.3 Correo 3 — El pensamiento

Borrador: [`correos/correo3.md`](correos/correo3.md). Apartados del guion §11: meditación e interiorización → porqué (trabajar el pensamiento) → para qué (ego y agregados psicológicos) → ancla → cierre al correo 4.

**Material de origen:** el *camino* de `/filosofia` (`Filosofia.vue:489-510`): autoobservación («contemplar pensamientos, emociones y reacciones sin justificarlos ni rechazarlos, reconociendo la influencia del ego…»), interiorización («aquietar el pensamiento recurrente…»), autodescubrimiento («no tanto descubrir algo nuevo como recordar…»); y las advertencias del mismo bloque (`Filosofia.vue:234-259`): ninguna técnica sustituye a la autoobservación cotidiana; «conocer, experimentar, contrastar y obtener las propias conclusiones».

**Dos señales abiertas antes de enviar:**

1. **Cita sin verificar:** *«Lo más importante es trabajar el pensamiento: ahí está todo.»* viene del guion del proyecto; **no aparece en esta base** (búsqueda de «trabajar el pensamiento / revolución del pensamiento» sin resultados en `resources/`). En el borrador se baja a prosa («Lo más importante, dicen, es trabajar el pensamiento»); si se quiere como cita textual de los Guías, hay que confirmar el origen en los libros/comunicados.
2. **«Mañana:» — resuelto.** La cadencia se cerró en **1 correo al día** (§10), así que los cierres *«Mañana:»* son correctos tal y como están.

**Decisión de enlace:** el guion ponía el ancla en el glosario; el correo nombra los términos (*autoobservación*, *interiorización*, *meditación*, *Juul*) como texto llano y deja **`/meditaciones`** como único enlace principal, que es la puerta de entrada práctica y coincide con el cierre del correo 2. El glosario queda mencionado sin enlace, para no partir el foco.

### 12.4 Correo 4 — Cómo se vive en el día a día

Borrador: [`correos/correo4.md`](correos/correo4.md). Apartados del guion §11: humildad y bondad como práctica → responde *¿qué se espera de mí?* → ancla glosario → cierre al correo 5.

**Material de origen:**

- *«No tratan de dirigiros. Ni de componer vuestras vidas. Ni de deciros lo que tenéis que hacer ni pensar… únicamente sugieren»*: `preguntas1.html:933-937` (Shilcars, ref. 097). Es la respuesta literal a *¿qué se espera de mí?* y, de paso, desactiva la duda de control sin nombrarla.
- *«Lo más importante es la humildad. El saberse que no saben nada…»*: `preguntas2.html:2446-2453` (ref. 035). La humildad como base, no como virtud decorativa.
- *«Desapego, espontaneidad y bondad y hermanamiento»* + *«concordia, amistad, hermandad»* + *«Trabajad en vosotros mismos, pero no de forma individual, sino grupal»*: `preguntas2.html:2603-2624` (ref. 102). La bondad como tono y el trabajo grupal.
- *«Humildad y equilibrio: aceptar que sabemos muy poco, abrirnos a nuevas posibilidades sin dogmatismo…»*: `Filosofia.vue:495-499`.
- *«En TSEYOR no caminas solo… cada cual desde su singularidad, todas hacia un mismo horizonte»*: `Portada.vue:215`.

**Hueco del entorno familiar (§11.3):** el correo **no lo cierra**. Responder *¿qué se espera de mí?* cubre el control personal, pero el correo no afirma nada sobre el entorno del lector (familia, amigos) porque no hay posición verificada en la base para sostenerla. Si el proyecto quiere cerrarlo aquí —una frase tipo *«tu vida sigue siendo tuya; nadie te pide convencer a nadie»*—, hay que confirmar primero que esa es la postura de la casa; si no, el hueco queda abierto y puede ir al correo 5, el institucional.

**Decisión de enlace:** aquí sí va **`/glosario`** como enlace principal, como quería el guion (en el correo 3 el ancla era la meditación práctica). Los términos se nombran como texto llano; el enlace único lleva al glosario completo.

### 12.5 Correo 5 — TSEYOR, lo que hacemos

Borrador: [`correos/correo5.md`](correos/correo5.md). Pieza de credibilidad institucional (§11.4). Apartados del guion §11: ONG y centros → contactismo ampliado → cómo se juzga → cierre con *«No nos creáis, comprobadlo.»*

**Material de origen:**

- *«¿Cómo puedo estar seguro de que el contacto es auténtico?»* — *«Nadie os podrá asegurar que el contacto sea veraz… el proceso egoico que invalida muchas veces la objetividad»*: `preguntas1.html:1492-1496` (ref. 070).
- *«¿Qué hay de las pruebas o fotografías?»* — *«no se trata de mostrar ni de dar pruebas… a personas que, aunque lo desean, no lo necesitan aún»* + apertura mental y cambio de esquemas: `preguntas1.html:1640-1644` (ref. 009).
- *«¿Por qué a veces dicen cosas contradictorias?»* — paradojas y lógica adimensional vs. dual; electrón onda/partícula: `preguntas1.html:2053-2064` (GLO).
- *«No hay trance ni pérdida de consciencia — es una comunicación lúcida y verificable»* y *«no te pedimos que creas, sino que examines la evidencia: más de 40 años de comunicados públicos, cientos de libros, psicografías y testimonios»*: FAQ de portada, `Portada.vue:408, :417`.
- Avistamientos *«previa cita»* con día, hora y lugar; fotografía del **31 de agosto de 1982**; nave a pocos metros en la explanada de Montserrat el **8 de junio de 2001**; Josep → Chac-Mool Puente, Marisa → Sala: `/origenes-de-tseyor` (`QuienesSomos/OrigenesDeTseyor.vue:44-166`).

**Decisiones y señales:**

1. **Enlace único:** `/quienes-somos`, como manda el guion. *Alternativa a considerar:* `/origenes-de-tseyor`, que es donde viven las fotografías de los avistamientos y la cronología; si el proyecto prefiere enlazar directamente la evidencia en lugar de la institución, se cambia un solo titular.
2. **Hueco del compromiso (§11.3):** el correo lo cierra con una frase — *«¿y qué se te pide a cambio? Tiempo y atención para juzgar por ti mismo. Nada más.»* Es coherente con la gratuidad ya dicha en el correo 1 y con el criterio de filtro; no inventa requisitos de la casa.
3. **El hueco del entorno familiar (§11.3) sigue abierto** — no se toca aquí.
4. **El cierre reutiliza la frase de los Guías**, que abre el correo 1 y cierra el 5 (§11.4), cerrando el arco de autenticidad.
5. «Cientos de libros» viene de la FAQ de portada (`Portada.vue:417`); en producción hay más de 400, pero se mantiene la fórmula de la web para no meter cifra en disputa dentro del correo.

### 12.6 Correo 6 — Los orígenes

Borrador: [`correos/correo6.md`](correos/correo6.md). Apartados del guion §11: cómo empezó todo → anécdota sacada del libro → ancla del libro → cierre al correo 7.

**La historia** (*La luz del 4 de noviembre*, capítulo 13) la aportó el proyecto y va casi literal: solo se corrigió la puntuación del *«Entonces ocurrió algo maravilloso,»* (quedaba incompleto) y el acento de *aún*. No reescribir sin pedido: es voz ajena.

**Tres señales antes de enviar:**

1. **Enlace del libro PENDIENTE.** El correo lleva un marcador *[Enlace al libro — pendiente de que se publique]*. Mismo estado que la dependencia anotada en §11: el libro no está ni en la web ni en la base local. No se envía hasta cerrar slug y publicación.
2. **Atribución del narrador — RESUELTA.** Confirmado por el proyecto: **Chac-Mool Puente y Josep Mirosa son la misma persona** — Josep Mirosa es el nombre civil y Puente su nombre simbólico (coherente con `OrigenesDeTseyor.vue:159-160`: *«Josep recibió más adelante su nombre espiritual Puente»*). La intro del correo 6 lo deja explícito («nuestro canalizador… que entonces firmaba con su nombre civil, Josep Mirosa»), lo que además hace comprensible la cita de prensa que cierra el capítulo. El cierre del correo 5 («una anécdota de nuestro canalizador») queda confirmado tal y como está.
3. **Melinus sí cruza con la web:** el personaje que aparece en la historia como tutor existe también en `/origenes-de-tseyor` (*«Melinus se presentó como miembro de la Confederación…»*, `OrigenesDeTseyor.vue:63`). Coherencia de la casa, no una afirmación nueva del correo. Sin segundo enlace (regla de enlace único).

### 12.7 Correo 7 — La invitación

Borrador: [`correos/correo7.md`](correos/correo7.md). Apartados del guion §11: ser miembro y «participar en esta aventura cósmica» → curso holístico → requisitos/condiciones → Sociedades Armónicas como destino → gratuidad (segunda y última) → sin compromiso y baja.

**Datos verificados en `/cursos`:** *+1.200 personas han realizado este curso* (`Cursos/Index.vue:11`), *100% gratuito* (`:25`), *Tutelado / En comunidad / 7 capítulos* (`:29-37`), *«puerta de entrada a una filosofía viva… donde todos aprendemos de todos»* (`:20`), y las condiciones literales *«Empezamos cuando recibimos solicitudes de tu zona, online o presencial. Sin compromiso, sin dogmas, sin coste. Solo tú y tu experiencia.»* (`:369`).

**Señales antes de enviar:**

1. **«Participar en esta aventura cósmica»**: la frase viene de la propia tabla de decisiones §10 (*Oferta final, correo 7*), así que es elección del proyecto, no un invento del borrador; aun así no aparece en esta base (`resources/` sin resultados), así que conviene confirmar que existe en producción o asumirla como propia. Dato relacionado: la casa sí tiene *«La aventura del despertar»* como título de un comunicado (`comunicados.lst:201`, comunicado 182, 2004), si se prefiere una fórmula verificada.
2. **«Hazte miembro… crear cuenta»** está formulado con generalidad a propósito: la web tiene usuarios registrados y contenidos restringidos a miembros (`ComunicadosInteriorizacion/Index.vue:14`), pero no se ha inventado ningún beneficio concreto de la membresía. Si el proyecto quiere enumerar lo que da ser miembro, que lo diga.
3. **Enlace único: `/cursos`**, con el botón *Inscribirme* dentro de esa página. La ruta real de inscripción es `cursos.inscripcion.nueva` (`Cursos/Index.vue:41`); si se prefiere enlazar directo al formulario, sustituir el titular.
4. **Sociedades Armónicas** cierra la promesa del subtítulo de portada (*cómo puedes ayudar / cómo llegar a vivir en las Sociedades Armónicas*) con una sola mención, como se decidió en §11.

---

## 13. Pendiente — checklist consolidada

Lista única de lo que falta. Se actualiza al ir resolviendo; las secciones anteriores conservan el detalle.

### Antes de enviar la secuencia

- [ ] **Publicar el libro de la biografía** de Chac-Mool Puente y cerrar su slug. Sustituir el marcador *[Enlace al libro — pendiente de que se publique]* en [`correos/correo6.md`](correos/correo6.md) (§12.6). **Bloqueante: el correo 6 no se envía sin esto.**
- [ ] **Confirmar la frase «participar en esta aventura cósmica»** en el correo 7: o existe en producción, o se asume como propia. Alternativa verificada: *«La aventura del despertar»* (comunicado 182, `comunicados.lst:201`) (§12.7).
- [ ] **Revisión humana de los 7 archivos** de [`correos/`](correos/). Son los textos editables a mano y la fuente de verdad; este documento solo guarda contexto.
- [ ] **Hueco del entorno familiar** (§11.3): sigue sin asignar a ningún correo — requiere que el proyecto confirme su postura antes de escribirlo.
- [ ] **«Hazte miembro» sin beneficios enumerados** (§12.7): si el proyecto quiere detallar qué da ser miembro, que lo diga.
- [ ] **Decidir el enlace del correo 7**: `/cursos` (actual) o directo al formulario `cursos.inscripcion.nueva` (§12.7).

### Requisitos de migración (cuando esta beta sustituya a producción)

- [ ] **Redirección `/el-rayo-sincronizador` → `/filosofia`**: el correo 2 enlaza `https://tseyor.org/el-rayo-sincronizador`, que existe hoy en producción; si desaparece sin redirección, los correos ya enviados darían 404 (§11.1).
- [ ] **Alinear slugs de libros**: `/libros/curso-holistico-tseyor` (local) frente al slug de producción; el libro «Los Guías Estelares» no está en la base local (§11.1).

### Pospuesto a propósito (no olvidarlo)

- [ ] **Sección de portada: volver al boletín normal — HECHO el 2026-10-19** (revertido en el working tree, nunca llegó a commit): la sección «7 correos para conocer qué es Tseyor» con props `cta`/`ayuda` de `Suscribe` se revirtió al boletín general («Boletín / Mantente al día») y `Suscribe.vue` volvió a HEAD. Los 7 correos quedan **pospuestos hasta nuevo aviso**; retomar decisión de §5.1.3 cuando el proyecto lo indique. (Se conservan en la portada los cambios de copy de §11.2/§11.4: cita «No nos creáis, comprobadlo» y FAQ «¿Es Tseyor una religión?».)
- [ ] **Backend de la secuencia**: rediseño de `Suscribir()` (sobrescribe `servicio`), tabla de pasos y disparo automático desde la portada. Pospuesto por decisión explícita.
- [ ] **Modalidad *alta automática al inscribirse al curso* — DISEÑADA en §15**, faltan por implementar: página de confirmación tras el formulario con la oferta de la serie (sin tocar el formulario), deduplicado por email (alternativa = libro «Los Guías Estelares»), selector de **variante del correo 7** por `origen` y KPIs separados por segmento. Sigue en pie la idea de **retrasar la solicitud al tutor 2-3 días** — decidir su orden respecto a la serie (§15.3).
- [ ] **Medición propia de la secuencia**: **diseño de base de datos resuelto en §14** (tablas `secuencia_correos`, `secuencia_suscripciones`, `secuencia_envios` + atribución en `inscripciones`; KPIs, gráfico de bajas por correo y top 3). Falta **implementarlo** (migraciones, captura de altas/envíos/bajas, pantalla de informe). GA4 no sirve como embudo: la mayoría son miembros recurrentes del grupo.

*Nota de entorno: Engram estuvo caído durante este trabajo (error de registro de sesión en `mem_save`); mientras siga así, este documento es la única fuente de contexto.*

---

## 14. Seguimiento y estadísticas — diseño de base de datos

**Qué hay que poder responder** (petición del proyecto):

1. Cuántas personas se anotan a la introducción de 7 correos, y desde dónde (`origen`).
2. Cuántas se dan de baja.
3. **En qué número de correo se dan de baja** — vista tipo gráfico.
4. Cuántos superan los 7 correos y **se inscriben al curso desde el enlace**, y cuántos no.
5. **Top 3 de correos que generan más bajas** (y su tasa real, no solo el recuento).
6. Como opcional: aperturas y clics por correo.

GA4 no sirve (§10): la medición es propia, con token por persona y registro en la base.

### 14.1 Tablas nuevas

Tres tablas propias de la secuencia, aisladas del sistema de boletines actual (así no se toca `suscriptores.servicio` — el rediseño de `suscribir()` sigue pospuesto).

**`secuencia_correos`** — catálogo de los 7 correos (una fila por correo, permitiendo editar asunto/enlace sin tocar envíos):

| Campo | Tipo | Notas |
|---|---|---|
| `id` | pk | |
| `numero` | int, unique | 1..7 |
| `slug` | string | p.ej. `guias-estelares` |
| `asunto` | string | espejo de `correos/correoN.md` |
| `enlace_principal` | string | único por correo (regla de §11) |
| `activo` | bool | para pausar un correo sin romper la serie |

**`secuencia_suscripciones`** — una fila por persona anotada. **Es la tabla madre**:

| Campo | Tipo | Notas |
|---|---|---|
| `id` | pk | |
| `email` | string | |
| `token` | string, unique | baja en 1 clic + atribución de enlaces |
| `origen` | string | `portada`, `cursos`, `manual`, … |
| `estado` | enum | `en_secuencia`, `completada`, `baja`, `rebotada` |
| `correo_actual` | int, default 0 | último número enviado (0..7) |
| `fecha_alta` | datetime | |
| `fecha_baja` | datetime, nullable | |
| **`baja_en_correo`** | int, nullable | **1..7 — clave para el gráfico y el top 3** |
| `motivo_baja` | string, nullable | opcional |
| `inscrito_curso` | datetime, nullable | fecha de conversión |

**`secuencia_envios`** — una fila por `suscripción × correo` (necesaria para saber a quién llegó qué, y para calcular tasas):

| Campo | Tipo | Notas |
|---|---|---|
| `id` | pk | |
| `suscripcion_id` | FK → secuencia_suscripciones | |
| `correo_numero` | int | 1..7 |
| `enviado_at` | datetime | |
| `estado` | enum | `enviado`, `rebotado`, `fallido` |
| `apertura_at` | datetime, nullable | si se trackea (pixel) |
| `primer_clic_at` / `clic_url` | nullable | si se trackea |

- **Unique `(suscripcion_id, correo_numero)`** → idempotencia: un correo nunca se envía dos veces a la misma persona.
- Índice `(correo_numero, enviado_at)` → consultas por correo.

**Modificación en `inscripciones` (curso):** añadir `secuencia_suscripcion_id` (nullable) y `origen`. El enlace del correo 7 lleva `?s=<token>`; al inscribirse se persiste el token → **conversión atribuible a la secuencia**.

**`suscriptores` (existente) no se toca**: se mantiene como lista de boletín; la secuencia usa su propia tabla. Cuando se retome el rediseño pospuesto de `suscribir()` (sobrescribe `servicio`), decidir si se unifican.

### 14.2 Cómo se captura cada dato

| Dato | Captura |
|---|---|
| Alta | `INSERT` en `secuencia_suscripciones` con `origen` + `token` |
| Envío | `INSERT` en `secuencia_envios` (fallido/rebotado actualiza `estado`) |
| Baja | Enlace de baja con `token` → `estado='baja'`, `fecha_baja`, **`baja_en_correo = MAX(envios.correo_numero)`** de esa suscripción |
| Conversión al curso | `?s=<token>` en el enlace del correo 7 → `inscripciones.secuencia_suscripcion_id` + `inscrito_curso` |
| Apertura / clic (opcional) | pixel 1x1 y links con token — depende del canal de envío; se diseña, se implementa si el medio lo soporta |

### 14.3 KPIs y cómo se ven

| KPI | Consulta base |
|---|---|
| Altas totales y por origen/día | `COUNT(*)` agrupado por `fecha_alta`, `origen` |
| Bajas totales y tasa | `COUNT(*) WHERE estado='baja'` / altas |
| **Curva de retención — el gráfico pedido (estilo YouTube)** | `% que sigue suscrito tras cada correo`: `S(N) = 1 − COUNT(baja_en_correo ≤ N) / altas`. Línea que arranca en **100% en el correo 1** y baja escalón a escalón (correo 2, 3, … 7 y *serie completada*). Se ve de un vistazo en qué escalón cae más |
| **Escalones con mayor caída (el «top»)** | deltas entre tramos: `S(N−1) − S(N)`. El *top 3* son los tres deltas más grandes — sale derivado de la misma curva, no de otra consulta |
| Bajas absolutas por correo (complemento) | `SELECT baja_en_correo, COUNT(*) … GROUP BY baja_en_correo` → barras 1..7 (+ `0` = baja antes del primer envío) |
| Tasa de baja por correo (sin sesgo de alcance) | bajas en el correo N ÷ envíos del correo N — comparable entre correos aunque unos lleguen a menos gente |
| Embudo de finalización | altas → `estado='completada'` → `inscrito_curso IS NOT NULL`; dos tasas: % que termina la serie, % que convierte |
| Aperturas / clics por correo | agrupado sobre `secuencia_envios` si el tracking está activo |

**Vista de informe (pantalla admin):** (a) embudo de 4 barras — anotados → completaron los 7 → inscritos al curso → tasa; (b) **curva de retención estilo YouTube** — línea desde el 100% en el correo 1 descendiendo escalón a escalón, con las caídas marcadas (el gráfico principal que pidió el proyecto); (c) tabla de los **3 escalones con mayor caída**, tomados de la misma curva; (d) altas recientes. Alerta opcional si la tasa de baja de un correo supera un umbral.

### 14.4 Notas de diseño

- **Nada se borra**: las bajas cambian de estado; las filas se conservan para auditoría y series históricas.
- **Token único por suscripción**: sirve a la vez para baja en 1 clic, atribución de inscripción al curso y clics.
- **Tablas aisladas** del sistema de boletines: implementar esta sección no exige tocar `suscribir()` (que sigue pendiente de rediseño).
- El alta de la portada debe pasar a `secuencia_suscripciones` con `origen='portada'` — hoy la portada escribe en `suscriptores` vía `Suscribir` (§el rediseño pospuesto).

---

## 15. Modalidad de entrada: los 7 correos al inscribirse al curso

§10 dejó esta vertiente *«aparte»*; aquí queda diseñada. Ocurre cuando el interesado **rellena el formulario de inscripción del curso holístico**: a partir de ahí puede empezar a recibir la misma serie de 7 correos.

### 15.1 Decisiones del proyecto

1. **El formulario NO se toca.** Se valoró pedir la suscripción dentro del propio formulario y se descarta: **crea fricción** en el momento en que el interesado ya ha dado el paso más grande. El formulario se queda tal y como está.
2. **La oferta va DESPUÉS del formulario**, en la página de confirmación que aparece tras enviar la inscripción: *«¿Quieres recibir también la serie de 7 correos?»* — un clic. El email ya está en el formulario, así que **no se le pide escribirlo otra vez**; el enlace lleva el token precargado.
3. **No repetir la oferta si ya la está recibiendo.** Deduplicado **por email** contra `secuencia_suscripciones`:
   - el email **no está** en la secuencia → se ofrece la serie;
   - el email **ya está** → no se ofrece de nuevo (sería tonto); en su lugar se le ofrece una **alternativa: empezar a leer el libro «Los Guías Estelares»** (`/libros/los-guias-estelares`, enlace ya activo desde las páginas de los guías).
4. **El correo 7 no puede ser el mismo.** A este segmento no tiene sentido invitarle al curso holístico: **acaba de inscribirse** y está esperando el contacto con el tutor. La serie **se bifurca en el correo 7**.

### 15.2 Cómo se implementa con lo diseñado en §14

- **Segmento = `origen`.** `secuencia_suscripciones.origen = 'inscripcion-curso'` (la portada es `'portada'`). **No hace falta columna nueva**: el `origen` ya existía en el diseño.
- **Los correos 1-6 son idénticos** para ambos segmentos — por diseño (§11) no mencionan el curso en ningún punto. Solo el 7 varía.
- **Selector de variante:** el envío del correo 7 lee `origen` y elige la versión `portada` (la actual) o la versión `curso`.
- **Página de confirmación de inscripción** con la oferta condicionada: consulta por email en `secuencia_suscripciones`; rama A (ofrecer serie) / rama B (libro *Los Guías Estelares*).
- **KPIs separados por `origen`** (§14): la tasa de conversión al curso solo tiene sentido para `portada`; para `inscripcion-curso` el éxito de la serie es **completarla hasta el contacto con el tutor**, no inscribirse.

### 15.3 Borrador de apartados — variante del correo 7 (segmento curso)

1. **Confirmación:** tu inscripción está registrada.
2. **Qué pasa ahora:** cuando haya grupo en tu zona, el tutor se pondrá en contacto contigo (condiciones literales de `/cursos`: *«empezamos cuando recibimos solicitudes de tu zona, online o presencial»*).
3. **Mientras tanto:** el libro «Los Guías Estelares» y los contenidos libres como lectura.
4. **Cierre de la serie:** sin mención a inscribirse al curso, con la baja en un clic.

**Coordinación abierta:** esta variante choca o no con la idea ya planteada de **retrasar la solicitud al tutor 2-3 días** (§13) — si se retrasa, la ventana de los 7 correos y el contacto del tutor se solapan y hay que decidir el orden. Pendiente de decidir el proyecto.

