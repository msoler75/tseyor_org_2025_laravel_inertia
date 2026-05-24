# Propuesta de reorganización del menú principal TSEYOR

Objetivo: reorganizar `resources/js/navigation.js` para que la top bar acompañe mejor el rediseño de marca y conversión definido en `branding/BRANDING.md` y `branding/REDISENO.md`.

El menú debe ayudar a tres tipos de visitante:

- Persona nueva con inquietud espiritual: necesita entender rápido qué es TSEYOR, de dónde nace su filosofía y cómo empezar sin sentirse presionada.
- Persona investigadora: quiere acceder a biblioteca, comunicados, libros, guías, glosario y materiales de libre descarga.
- Miembro o usuario recurrente: necesita herramientas, equipos, salas, archivos, normativas y aplicaciones internas.

## Diagnóstico del menú actual

Estructura visible actual en desktop:

- `Novedades`
- `Biblioteca`
- `Formación`
- `Organización`
- `Comunidad`

Problemas principales:

- `Novedades` aparece como primera opción visible, pero en el rediseño es una vía de actualidad/retención, no el camino principal de conversión.
- `Curso Holístico Tseyor`, que es el CTA principal del sitio, está dentro de `Formación`; no aparece con suficiente fuerza en la top bar.
- `Blog`, que ayuda a conectar con imágenes y vida comunitaria, queda enterrado dentro de `Biblioteca`.
- `Guías Estelares`, que es un diferencial central de marca, está dentro de `Formación > Consulta`, demasiado oculto para su importancia.
- `Organización` suena administrativo. Para visitantes nuevos, `Quiénes somos` o `TSEYOR` comunica mejor.
- `Comunidad` mezcla una promesa emocional pública con herramientas internas. El submenu indica `Para miembros de Tseyor`, pero esa cabecera no se renderiza en desktop.
- Hay errores puntuales de implementación: iconos probablemente inválidos y `Tseyor Canva` apuntando a `http://localhost:8000`.

Principio de reorganización:

> La top bar no debe reflejar la estructura interna de la web, sino el recorrido mental del usuario: entender, confiar, explorar, empezar y volver.

## Top bar recomendada

Orden recomendado para desktop:

1. `Curso`
2. `Biblioteca`
3. `Filosofía`
4. `Quiénes somos`
5. `Blog`
6. `Novedades`
7. `Área miembros`

Si hay poco espacio en desktop o tablet horizontal:

1. `Curso`
2. `Biblioteca`
3. `Filosofía`
4. `Quiénes somos`
5. `Blog`
6. `Más`

Dentro de `Más` irían `Novedades`, `Eventos`, `Boletines`, `Contacto` y `Área miembros`.

Justificación:

- `Curso` primero: es el objetivo principal de conversión.
- `Biblioteca` segundo: es la vía de exploración libre y prueba de generosidad/conocimiento gratuito.
- `Filosofía` tercero: comunica el diferencial estelar, `sin dogmas` y profundidad.
- `Quiénes somos` cuarto: genera confianza institucional, ONG y comunidad.
- `Blog` quinto: aporta conexión humana, fotografías, encuentros y vida real.
- `Novedades` después: útil para retorno, pero menos importante para visitantes nuevos.
- `Área miembros` separada: evita que herramientas internas contaminen el camino público.

## Propuesta de submenús

### 1. Curso

Función: convertir a inscripción sin sonar comercial.

Items:

- `Curso Holístico Tseyor`
  - Ruta: `/cursos`
  - Descripción: `Aprende gratis la base de la filosofía de las estrellas, con acompañamiento y sin dogmas.`
- `Inscríbete gratis`
  - Ruta: `/inscripcion`
  - Descripción: `Comienza el curso online y recibe orientación para tus primeros pasos.`
- `Mis primeros pasos`
  - Ruta: `/mis-primeros-pasos`
  - Descripción: `Una guía para empezar a explorar TSEYOR con claridad.`
- `Preguntas frecuentes`
  - Ruta: `/preguntas-frecuentes`
  - Descripción: `Resuelve dudas habituales antes de empezar.`

Opcionales dentro del mismo submenu:

- `Eventos`
  - Ruta: `/eventos`
  - Descripción: `Consulta encuentros, convivencias y actividades próximas.`
- `Glosario`
  - Ruta: `/glosario`
  - Descripción: `Consulta términos clave de la filosofía TSEYOR.`

Notas:

- `Inscríbete gratis` debe tener estilo visual destacado dentro del submenu.
- Evitar `Formación` como etiqueta superior: suena más académico y menos directo.
- Si solo una opción de la top bar puede tener apariencia de CTA, debe ser `Curso` o `Inscríbete gratis`.

### 2. Biblioteca

Función: mostrar abundancia documental, libre descarga y origen de los contenidos.

Items:

- `Biblioteca Tseyor`
  - Ruta: `/biblioteca`
  - Descripción: `Libros, comunicados, audios y materiales de libre descarga.`
- `Comunicados`
  - Ruta: `/comunicados`
  - Descripción: `Comunicados recibidos de los Guías Estelares y la Confederación.`
- `Libros`
  - Ruta: `/libros`
  - Descripción: `Libros y monografías sobre la filosofía TSEYOR.`
- `Audios`
  - Ruta: `/audios`
  - Descripción: `Meditaciones, talleres, cuentos, canciones y materiales sonoros.`
- `Meditaciones`
  - Ruta: `/meditaciones`
  - Descripción: `Prácticas para el trabajo interior.`
- `Vídeos`
  - Ruta: `/videos`
  - Descripción: `Material audiovisual de meditaciones, talleres y encuentros.`
- `Psicografías`
  - Ruta: `/psicografias`
  - Descripción: `Láminas para el trabajo de abstracción.`

Opcionales:

- `Radio Tseyor`
  - Ruta: `/radio`
  - Descripción: `Escucha la radio online de TSEYOR.`
- `Galerías de arte`
  - Ruta: `/galerias`
  - Descripción: `Arte y creatividad de la comunidad.`

Notas:

- Sacar `Blog` de Biblioteca.
- Sacar `Descubre` de Biblioteca si se convierte en puerta editorial de la filosofía. Puede ir mejor en `Filosofía`.
- Reforzar descripciones con `libre descarga` y `Guías Estelares`.

### 3. Filosofía

Función: explicar el diferencial de TSEYOR: filosofía de las estrellas, Guías Estelares, Confederación y camino sin dogmas.

Items:

- `Nuestra filosofía`
  - Ruta: `/filosofia`
  - Descripción: `Filosofía de las estrellas, conciencia, autodescubrimiento y Sociedades Armónicas.`
- `Guías Estelares`
  - Ruta: `/guias`
  - Descripción: `Conoce a los tutores de la Confederación de Mundos Habitados de la Galaxia.`
- `Orígenes de TSEYOR`
  - Ruta: `/origenes-de-tseyor`
  - Descripción: `El origen del contacto y la historia del grupo.`
- `Comunicados`
  - Ruta: `/comunicados`
  - Descripción: `Lee la fuente documental de la filosofía TSEYOR.`
- `Glosario`
  - Ruta: `/glosario`
  - Descripción: `Términos clave para comprender el conocimiento TSEYOR.`
- `Descubre`
  - Ruta: `/descubre`
  - Descripción: `Temas clave presentados de forma introductoria.`

Notas:

- Este menú debe evitar sonar genérico. El texto debe decir `filosofía de las estrellas`, `Guías Estelares`, `Confederación` y `sin dogmas`.
- Si `Filosofía` fuera demasiado conceptual para top bar, alternativa: `Conocimiento`. Aun así, `Filosofía` es más directo y coherente con las páginas actuales.

### 4. Quiénes somos

Función: generar confianza institucional y humana.

Items:

- `Quiénes somos`
  - Ruta: `/quienes-somos`
  - Descripción: `Conoce la comunidad TSEYOR, su origen, propósito y forma de trabajar.`
- `ONG Mundo Armónico TSEYOR`
  - Ruta: `/ong`
  - Descripción: `Nuestra ONG sin ánimo de lucro y sus objetivos.`
- `Dónde estamos`
  - Ruta: `/donde-estamos`
  - Descripción: `Ubicaciones y contactos donde encontrar TSEYOR.`
- `Centros TSEYOR`
  - Ruta: `/centros`
  - Descripción: `Casas TSEYOR y Muulasterios en el mundo.`
- `Contactar`
  - Ruta: `/contactar`
  - Descripción: `Ponte en contacto con nosotros.`

Secundarios:

- `Asociación TSEYOR`
  - Ruta: `/asociacion`
  - Descripción: `Conoce la primera entidad de TSEYOR.`
- `Universidad TSEYOR de Granada`
  - Ruta: `/utg`
  - Descripción: `Conoce nuestra Universidad.`

Notas:

- Sustituir `Organización` por `Quiénes somos`.
- La ONG debe estar visible aquí, pero también como sello en home. En menú no hace falta que sea primer item si el usuario nuevo aún no sabe qué busca.
- `Dónde estamos`, `Centros` y `Contactar` deben estar en este grupo porque responden a confianza y presencia real.

### 5. Blog

Función: conexión humana y emocional.

Items:

- `Blog`
  - Ruta: `/blog`
  - Descripción: `Vivencias, encuentros, imágenes y vida de la comunidad TSEYOR.`
- `Galerías`
  - Ruta: `/galerias`
  - Descripción: `Fotografías, arte y memoria visual de la comunidad.`
- `Experiencias`
  - Ruta: `/experiencias`
  - Descripción: `Experiencias compartidas por miembros y participantes.`
- `Noticias`
  - Ruta: `/noticias`
  - Descripción: `Noticias y anuncios de la comunidad TSEYOR.`

Notas:

- El blog merece visibilidad propia porque el usuario ya indicó que ayuda a conectar enseguida con imágenes y fotos del grupo.
- Si la top bar queda demasiado larga, `Blog` podría integrarse en `Novedades`, pero no es lo ideal para conversión humana.

### 6. Novedades

Función: actualidad, retorno y boletín.

Items:

- `Novedades`
  - Ruta: `/novedades`
  - Descripción: `Los contenidos más recientes de TSEYOR.`
- `Eventos`
  - Ruta: `/eventos`
  - Descripción: `Cursos, convivencias y encuentros próximos.`
- `Boletines`
  - Ruta: `/boletines`
  - Descripción: `Boletines mensuales de la comunidad TSEYOR.`
- `Noticias`
  - Ruta: `/noticias`
  - Descripción: `Noticias y anuncios institucionales.`

Redes:

- `Facebook`
- `X`
- `YouTube`

Notas:

- `Eventos` puede aparecer aquí, pero debe tener un acceso contextual más fuerte en home cuando hay eventos programados.
- `Boletines` debe estar cerca de `Novedades` porque ambos son retorno/continuidad.
- Si `Noticias` queda en `Blog`, evitar duplicarla aquí o mantenerla solo en un lugar principal.

### 7. Área miembros

Función: herramientas, equipos y documentación interna.

Items:

- `Equipos`
  - Ruta: `/equipos`
  - Descripción: `Equipos de trabajo.`
- `Usuarios`
  - Ruta: `/usuarios`
  - Descripción: `Listado de usuarios.`
- `Informes`
  - Ruta: `/informes`
  - Descripción: `Informes de los equipos.`
- `Salas`
  - Ruta: `/salas`
  - Descripción: `Salas virtuales de reuniones.`
- `Normativas`
  - Ruta: `/normativas`
  - Descripción: `Estatutos, protocolos y normativas.`
- `Archivos`
  - Ruta: `/archivos`
  - Descripción: `Archivos y documentos en carpetas.`
- `Muular Electrónico`
  - Ruta: `/muular-electronico`
  - Descripción: `Intercambio de bienes y servicios.`
- `Sello de TSEYOR`
  - Ruta: `/sello`
  - Descripción: `Meditación con el sello de TSEYOR.`
- `Juego del puzle`
  - URL: `https://puzle.tseyor.org/`
  - Descripción: `Juego del puzle con las láminas de abstracción.`
- `TSEYOR Canva`
  - Ruta recomendada: `/tseyor-canva`
  - Descripción: `Aplicación para el diseño de carteles o anuncios.`

Notas:

- Renombrar `Comunidad` a `Área miembros` reduce confusión.
- Si algunas rutas requieren login o tienen sentido solo para miembros, el submenu debe indicarlo con una cabecera visible: `Para miembros de TSEYOR`.
- El componente desktop `NavSubmenu.vue` debería renderizar `submenu.header` para que esta intención quede clara.

## Variante compacta recomendada

Si se quiere reducir la top bar a seis opciones:

1. `Curso`
2. `Biblioteca`
3. `Filosofía`
4. `Quiénes somos`
5. `Blog`
6. `Más`

`Más` contendría:

- `Novedades`
- `Eventos`
- `Boletines`
- `Noticias`
- `Área miembros`
- `Contactar`

Esta variante es mejor para tablet horizontal y escritorios estrechos. En desktop amplio, la versión de siete opciones es más clara.

## Reglas para desktop, tablet y móvil

Desktop:

- Mostrar el logo como enlace a home.
- Mantener top bar con máximo 6-7 opciones visibles.
- El submenu debe priorizar 2-3 columnas escaneables, no listas densas de 20 enlaces sin jerarquía.
- `Curso` debe tener tratamiento visual más fuerte que el resto, aunque no necesariamente como botón rectangular.
- `Área miembros` debe quedar al final.

Tablet:

- No copiar la navegación desktop si los textos quedan apretados.
- En tablet horizontal, mantener `Curso`, `Biblioteca`, `Filosofía`, `Quiénes somos`, `Blog` si caben; agrupar el resto en `Más`.
- En tablet vertical, usar navegación colapsada, pero dejar visible un CTA compacto si el diseño lo permite: `Curso` o `Inscríbete gratis`.
- Evitar submenús gigantes flotantes en tablet. Mejor panel lateral o dropdown de ancho controlado.

Móvil:

- Menú en panel lateral o drawer.
- Primeras opciones del drawer:
  1. `Curso Holístico`
  2. `Inscríbete gratis`
  3. `Biblioteca`
  4. `Guías Estelares`
  5. `Quiénes somos`
  6. `Blog`
- `Área miembros` debe aparecer más abajo, separado por título.
- No ocultar `Inicio`; en móvil sí conviene mantenerlo visible como primera o segunda opción, dependiendo del diseño.

## Cambios concretos sobre `navigation.js`

Cambios de estructura:

- Cambiar `Formación` por `Curso`.
- Cambiar `Organización` por `Quiénes somos`.
- Cambiar `Comunidad` por `Área miembros`.
- Crear un tab superior `Filosofía`.
- Crear un tab superior `Blog`.
- Bajar `Novedades` detrás de los caminos principales.
- Sacar `Blog` de `Biblioteca`.
- Mover `Guías Estelares` desde `Curso/Formación` a `Filosofía`.
- Mover `Descubre` desde `Biblioteca` a `Filosofía`.
- Mantener `Eventos` en `Novedades`, pero recordar que en home debe tener CTA condicional propio.

Cambios de copy:

- `Inscríbete a nuestro curso` -> `Inscríbete gratis`.
- `Aprende gratis nuestra filosofía` -> `Comienza el curso online y explora sin dogmas.`
- `Conoce nuestro curso de origen estelar` -> `Aprende gratis la base de la filosofía de las estrellas.`
- `Conoce la filosofía cósmico crística` -> `Filosofía de las estrellas, autodescubrimiento y Sociedades Armónicas.`
- `Conoce a los tutores de la Confederación` -> `Conoce a los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia.`
- `Artículos de nuestros blog` -> `Vivencias, imágenes y vida de la comunidad TSEYOR.`

Cambios técnicos:

- Corregir icono `ph:ph:graduation-cap-duotone` a `ph:graduation-cap-duotone`.
- Corregir icono `ph:shield-cheph:tree-duotone` a un icono válido, por ejemplo `ph:tree-duotone`, `ph:shield-check-duotone` o `ph:users-three-duotone`.
- Cambiar `APP_TSEYOR_CANVA_URL = "http://localhost:8000"` por `/tseyor-canva` o por una variable de entorno.
- Renderizar `submenu.header` en `NavSubmenu.vue`, especialmente para `Área miembros`.
- Revisar si todos los tabs con submenu deben tener también ruta propia. La experiencia sería más consistente si el click abre submenu y el primer item lleva a la landing del grupo.

## Estructura sugerida para implementación

Ejemplo conceptual de top level:

```js
[
  { title: "Inicio", url: "/", onlyAside: true },
  { title: "Curso", route: "cursos", submenu: ... },
  { title: "Biblioteca", route: "biblioteca", submenu: ... },
  { title: "Filosofía", route: "filosofia", submenu: ... },
  { title: "Quiénes somos", route: "quienes-somos", submenu: ... },
  { title: "Blog", route: "blog", submenu: ... },
  { title: "Novedades", route: "novedades", submenu: ... },
  { title: "Área miembros", submenu: ... }
]
```

Regla: las rutas principales de top bar deben coincidir con páginas fuertes del rediseño:

- `/cursos`
- `/biblioteca`
- `/filosofia`
- `/quienes-somos`
- `/blog`
- `/novedades`

## Riesgos y mitigaciones

Riesgo: demasiadas opciones en top bar.

Mitigación: usar variante compacta con `Más`, especialmente en tablet o escritorio estrecho.

Riesgo: `Filosofía` y `Biblioteca` se solapan.

Mitigación: `Filosofía` explica el marco y el origen estelar; `Biblioteca` contiene materiales y descargas.

Riesgo: `Blog` y `Novedades` se solapan.

Mitigación: `Blog` debe contar vida humana, imágenes y experiencias; `Novedades` debe ser actualidad, eventos y boletín.

Riesgo: `Área miembros` parezca cerrada o excluyente.

Mitigación: mantenerla al final y usar texto claro. Para visitante nuevo, la comunidad se explica en `Quiénes somos` y `Blog`; el área operativa queda separada.

## Recomendación final

Implementar primero la variante de siete opciones:

`Curso` · `Biblioteca` · `Filosofía` · `Quiénes somos` · `Blog` · `Novedades` · `Área miembros`

Después de implementarla, validar visualmente:

- Desktop amplio: que no se vea saturada.
- Tablet horizontal: que no obligue a textos apretados.
- Tablet vertical y móvil: que el drawer priorice Curso, Biblioteca, Guías Estelares y Blog.
- Home: que el menú no contradiga el CTA principal del hero.

Esta reorganización hace que el menú deje de ser un índice interno y pase a funcionar como un mapa de decisión para el usuario nuevo.
