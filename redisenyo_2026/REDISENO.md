# Rediseño de páginas principales TSEYOR

Objetivo: reorganizar `/`, `/quienes-somos`, `/cursos` y `/filosofia` para que cada página tenga una función clara, reutilizando el contenido existente en `resources/js/Pages` y evitando una portada demasiado dispersa.

Principio de conversión:

- CTA principal del sitio: Curso Holístico Tseyor.
- CTA secundario: Biblioteca Tseyor.
- CTA de conexión humana: Blog.
- CTA de actualidad/retención: Novedades y boletín.
- CTA contextual urgente: Próximos eventos, solo cuando existan eventos programados.
- Prueba social nueva: Testimonios reales de participantes, alumnos o miembros de la comunidad.
- Confianza institucional: TSEYOR debe presentarse claramente como ONG, sin ánimo de lucro, con actividades no lucrativas y contenidos de libre descarga.
- Promesa de tono: espiritualidad sin dogmas, basada en experimentación personal, retroalimentación y libertad interior.

## Revisión estratégica de marketing y UX

La portada actual tiene buen material emocional y visual, pero mezcla demasiadas rutas sin jerarquía. Para mejorar experiencia y objetivos, la estructura debe responder a cuatro preguntas en este orden:

1. ¿Qué es TSEYOR y por qué me importa?
2. ¿Puedo confiar o explorar sin compromiso?
3. ¿Qué hago ahora según mi nivel de interés?
4. ¿Cómo sigo conectado si todavía no quiero inscribirme?

Jerarquía recomendada de CTAs:

- **Primario global**: `Comenzar el Curso Holístico` / `Inscríbete gratis`.
- **Secundario global**: `Explorar la Biblioteca`.
- **Contextual urgente**: `Próximos eventos`, visible solo si hay eventos programados.
- **Conexión humana**: `Leer Blog`.
- **Retención**: `Ver Novedades` / `Suscribirme al boletín`.

Reglas de presentación:

- Cada sección debe tener un único objetivo principal. Si hay dos botones, el segundo debe ser claramente secundario.
- El hero debe comunicar valor en 5 segundos: título de resultado, texto breve, CTA principal, CTA secundario y acceso condicional a eventos.
- En los primeros 30 segundos debe quedar claro el diferencial de TSEYOR: su filosofía proviene de las estrellas, de los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia. No debe parecer una filosofía espiritual genérica.
- El carácter de ONG sin ánimo de lucro debe aparecer en la parte alta de la home, antes de pedir inscripción.
- La gratuidad debe ser específica: curso gratuito, contenidos de libre descarga, libros PDF y audios disponibles libremente.
- La comunidad debe explicarse como comunidad conformada por miembros de la ONG, no como un grupo informal ambiguo.
- `Sin dogmas` debe aparecer de forma temprana y transversal: home, `/quienes-somos`, `/cursos`, `/filosofia` y testimonios.
- La expresión `sin dogmas` debe ir acompañada de su sentido práctico: no creer por creer, experimentar, comprobar, compartir y sacar conclusiones propias.
- Las imágenes deben mostrar resultado humano o experiencia real: grupo, encuentros, biblioteca, meditación, comunidad. Evitar imágenes puramente abstractas cuando la sección necesita confianza.
- Blog y eventos deben tener imágenes visibles porque ayudan a conectar con personas reales.
- Novedades y boletín van al final: son útiles para retorno, pero no explican la esencia a un visitante nuevo.
- En móvil, el CTA principal debe ocupar ancho completo. `Próximos eventos` puede ir justo debajo si existe, con estilo secundario destacado.

## Promesa transversal: sin dogmas

`Sin dogmas` no debe ser solo un claim de portada. Debe acompañar todo el recorrido del usuario y aparecer justo donde reduce una duda o fricción:

- **Home**: promesa inicial. TSEYOR se presenta como espiritualidad viva, libre y sin dogmas.
- **Caminos de entrada**: el curso y la biblioteca deben comunicar que se puede empezar a explorar sin obligación de creer ni pertenecer de inmediato.
- **/quienes-somos**: prueba de identidad. Explicar que la comunidad se basa en experimentación, retroalimentación, libertad interior y miembros de una ONG sin ánimo de lucro.
- **/cursos**: garantía de inscripción. Aclarar que el curso acompaña y ordena, pero no impone creencias.
- **/filosofia**: fundamento conceptual. Subir `Interesa la comprobación` para explicar que no se trata de creer por creer, sino de experimentar y comprobar.
- **Testimonios**: prueba social. Seleccionar testimonios que transmitan acompañamiento, respeto al proceso individual y ausencia de imposición.
- **FAQ**: resolver la objeción de forma directa con una pregunta del tipo `¿TSEYOR es dogmático?`.

Formulación recomendada:

> No se trata de creer por creer. TSEYOR propone experimentar, compartir, comprobar y extraer cada uno sus propias conclusiones.

## Diferencial: filosofía de las estrellas

El rediseño debe posicionar claramente que TSEYOR no presenta una filosofía espiritual común ni solo crecimiento personal. Su diferencial es que la filosofía procede de las estrellas, de nuestros Guías Estelares de la Confederación de Mundos Habitados de la Galaxia.

Este mensaje debe aparecer:

- **Hero de home**: en el H1 o subtítulo principal.
- **Primeras secciones de home**: como bloque específico antes de curso/biblioteca.
- **/quienes-somos**: como fundamento de identidad.
- **/cursos**: como base del Curso Holístico.
- **/filosofia**: como origen explícito de la filosofía Tseyor.
- **SEO y contenido escaneable**: usar términos como `filosofía de las estrellas`, `Guías Estelares`, `Confederación de Mundos Habitados de la Galaxia`, `comunicados interdimensionales`.

Formulación recomendada:

> La filosofía Tseyor nace de las comunicaciones con nuestros Guías Estelares de la Confederación de Mundos Habitados de la Galaxia. Por eso no es una filosofía espiritual común: es una invitación al autodescubrimiento desde una perspectiva cósmica, libre y sin dogmas.

Fuentes de texto usadas:

- `resources/js/Pages/Portada.vue`
- `resources/js/Pages/Presentacion/QuienesSomos.vue`
- `resources/js/Pages/Presentacion/Filosofia.vue`
- `resources/js/Pages/Cursos/Index.vue`
- `resources/js/Pages/Biblioteca.vue`
- `resources/js/Pages/Entradas/Index.vue`
- `resources/js/Pages/Novedades.vue`
- `branding.htm`

## `/` Home

Función de la página: explicar en pocos segundos qué es TSEYOR y dirigir a la persona hacia curso, biblioteca, blog, novedades o boletín. La home no debe intentar contener toda la filosofía; debe orientar.

### Auditoría `homepage-generator` contra `BRANDING.md`

Estado general: **alineación alta, con ajustes de compresión y SEO pendientes**.

La home propuesta cumple los ejes centrales de marca:

- Diferencial claro: `filosofía de las estrellas`, `Guías Estelares`, `Confederación de Mundos Habitados de la Galaxia`.
- Confianza institucional: ONG sin ánimo de lucro, comunidad de miembros, libre descarga.
- Conversión: Curso Holístico como CTA principal.
- Exploración: Biblioteca como CTA secundario fuerte.
- Conexión humana: Blog, testimonios y eventos.
- Tono: sin dogmas, experimentación personal, retroalimentación.

Riesgos detectados:

- La secuencia inicial puede quedar demasiado larga si `Franja institucional`, `De dónde nace la filosofía Tseyor` y `TSEYOR en una mirada` se implementan como secciones completas. En homepage, deben funcionar como módulos compactos antes de que el usuario tenga que hacer demasiado scroll.
- El bloque `De dónde nace la filosofía Tseyor` es imprescindible, pero debe estar muy cerca del hero y no competir visualmente con él.
- Falta especificar metadatos SEO, H1 y schema de homepage.
- Falta definir una prueba de "primeros 5 segundos" y "primeros 30 segundos" para validar implementación.

Recomendación de implementación:

1. Hero potente con diferencial estelar, CTA curso, CTA biblioteca y eventos condicionales.
2. Debajo del hero, una franja compacta de sellos: `ONG sin ánimo de lucro`, `Sin dogmas`, `Libre descarga`, `Comunidad de miembros`.
3. Inmediatamente después, bloque breve `De dónde nace la filosofía Tseyor`, con CTA a `/guias` y `/comunicados`.
4. Después, `TSEYOR en una mirada` como social proof compacto con 3-4 datos.
5. Luego, caminos de entrada y secciones de conversión.

No convertir los primeros cuatro módulos en cuatro pantallas completas. La home debe orientar rápido.

Orden optimizado:

1. Hero con promesa clara y diferencial estelar: filosofía de las estrellas, Guías Estelares y Confederación.
2. Franja institucional: ONG, sin ánimo de lucro, libre descarga y comunidad de miembros.
3. Origen estelar de la filosofía: Guías Estelares, Confederación y comunicados.
4. Franja breve de confianza: más de 40 años, biblioteca, comunicados, comunidad.
5. Caminos de entrada: curso, biblioteca, blog y eventos si existen.
6. Curso Holístico como conversión principal.
7. Biblioteca como exploración libre.
8. Comunidad y Blog como conexión humana.
9. Testimonios como prueba social.
10. Filosofía como profundización.
11. Novedades y boletín como retorno.

Este orden corrige el problema de una portada muy contemplativa: primero orienta, luego seduce, y después profundiza.

### 1. Filosofía de las estrellas, conciencia sin dogmas

Tipo: Hero

Texto:

> La filosofía Tseyor nace de las comunicaciones con nuestros Guías Estelares de la Confederación de Mundos Habitados de la Galaxia.
>
> Nuestro proyecto es el despertar a una nueva consciencia y la creación de las Sociedades Armónicas, a través de la transmutación de nuestro pensamiento.
>
> Mundo Armónico TSEYOR es una ONG sin ánimo de lucro. Nuestra comunidad está conformada por miembros de la ONG que trabajan hacia la instauración de las Sociedades Armónicas de la Galaxia desde una espiritualidad viva, libre y sin dogmas.

Botones CTA:

- Principal: `Comenzar el Curso Holístico` -> `/cursos`
- Secundario: `Explorar la biblioteca` -> `/biblioteca`
- Condicional si hay eventos programados: `Próximos eventos` -> `/eventos`

Notas:

- Mostrar `ONG Mundo Armónico TSEYOR` como marca o sobrelínea, no necesariamente como H1. Como titular de conversión funciona mejor `Filosofía de las estrellas, conciencia sin dogmas`, porque comunica el diferencial y el beneficio.
- Mantener como primera pantalla, pero sustituir el CTA único actual `Quiénes somos` por un CTA de conversión.
- `Quiénes somos` puede quedar como enlace de navegación, no como CTA principal del hero.
- `Próximos eventos` debe mantenerse en el primer viewport cuando `hayProximosEventos` sea verdadero. No debe competir con el CTA principal: usarlo como botón auxiliar destacado, por ejemplo bajo los CTAs en móvil y como píldora flotante inferior derecha en escritorio, como en la portada actual.
- El texto del hero debe dejar claro que se trata de una ONG sin ánimo de lucro, pero sin convertir el hero en una explicación legal.
- El hero debe incluir `sin dogmas` o una fórmula equivalente muy cercana: `espiritualidad viva, libre y sin dogmas`.
- El hero debe dejar claro en menos de 30 segundos que la filosofía procede de los Guías Estelares y de la Confederación. Si esto queda solo en secciones inferiores, se pierde el principal factor diferencial.
- SEO: el H1 o subtítulo debe incluir `filosofía de las estrellas`, `Guías Estelares` o `Confederación de Mundos Habitados de la Galaxia`.

Opciones de copy para hero:

- Opción A H1: `Filosofía de las estrellas, conciencia sin dogmas`
- Opción A subtítulo: `La filosofía TSEYOR nace de las comunicaciones con nuestros Guías Estelares de la Confederación de Mundos Habitados de la Galaxia.`
- Opción B H1: `TSEYOR: filosofía estelar para el autodescubrimiento`
- Opción B subtítulo: `Una ONG sin ánimo de lucro que comparte conocimiento gratuito, libre descarga y un camino de retroalimentación sin dogmas.`
- Opción C H1: `Una filosofía cósmica, libre y sin dogmas`
- Opción C subtítulo: `Más de 40 años de comunicados, libros y experiencias con los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia.`

Recomendación: usar la opción A en portada porque comunica el diferencial de marca con más rapidez.

Imagen/diseño:

- Usar una imagen experiencial y humana antes que un fondo solo espacial. La composición de `PortadaNueva.vue` con cielo, montañas y personas funciona mejor si las personas quedan visibles desde el primer viewport.
- Si se mantiene `FondoEspacio`, añadir una capa visual o imagen secundaria de comunidad para que el visitante vea que hay personas reales detrás del mensaje.
- Evitar que el texto quede dentro de una tarjeta oscura muy pesada; usar una capa ligera de contraste y mantener mucho aire.

### 2. ONG sin ánimo de lucro y conocimiento libre

Tipo: Texto normal / franja institucional

Texto:

> Mundo Armónico TSEYOR es una ONG sin ánimo de lucro dedicada al desarrollo humano, el despertar espiritual y la ayuda humanitaria en sus vertientes material y espiritual.
>
> Todas nuestras actividades se sostienen desde el servicio y la participación de sus miembros. Los contenidos de TSEYOR, incluyendo libros PDF, comunicados, audios y meditaciones, son de libre acceso y descarga.
>
> La comunidad TSEYOR está conformada por miembros de la ONG que comparten este camino de autodescubrimiento, hermanamiento y retroalimentación, sin dogmas y desde la experimentación personal.

Botones CTA:

- Principal: `Conoce la ONG` -> `/ong`
- Secundario: `Explorar contenidos libres` -> `/biblioteca`

Notas:

- Debe aparecer inmediatamente después del hero.
- Motivo: reduce dudas sobre ánimo de lucro, pertenencia y acceso al conocimiento.
- Mostrar 4 sellos o chips visibles: `ONG sin ánimo de lucro`, `Sin dogmas`, `Contenidos de libre descarga`, `Comunidad de miembros`.
- Esta sección no debe ser larga; funciona como garantía de confianza.

### 3. De dónde nace la filosofía Tseyor

Tipo: Texto normal / bloque diferencial con imagen

Texto:

> La filosofía Tseyor proviene de las comunicaciones mantenidas con nuestros Guías Estelares, miembros de la Confederación de Mundos Habitados de la Galaxia.
>
> Todos nuestros materiales se basan en estas conversaciones, recibidas y preservadas durante más de 40 años, que nos guían con paciencia, amor y comprensión en este proceso de cambio de Era.
>
> Por eso TSEYOR no es una filosofía espiritual común: es una propuesta de autodescubrimiento con origen estelar, abierta a la experimentación personal y sin dogmas.

Botones CTA:

- Principal: `Conocer a los Guías Estelares` -> `/guias`
- Secundario: `Leer comunicados` -> `/comunicados`

Notas:

- Debe aparecer antes de la sección de caminos, curso y biblioteca.
- Motivo: el visitante debe reconocer el diferencial de TSEYOR en los primeros 30 segundos: Guías Estelares, Confederación y origen estelar de la filosofía.
- Esta sección ayuda a buscadores y usuarios a diferenciar TSEYOR de una propuesta genérica de crecimiento personal.
- Puede usar imagen `/almacen/medios/paginas/todos-los-guias.jpg` o una imagen cósmica clara, pero no puramente abstracta.
- Debe evitar un tono sensacionalista. El tono correcto es sereno, directo y transparente.

### 4. TSEYOR en una mirada

Tipo: Texto normal / franja de confianza

Texto:

> Más de 40 años de intercambio de mensajes, avistamientos y experiencias interdimensionales.
>
> Una biblioteca web con libros PDF, comunicados, audios, meditaciones y materiales de libre acceso y descarga.
>
> Una comunidad de miembros de la ONG que trabaja el autodescubrimiento, la ayuda humanitaria y la creación de Sociedades Armónicas desde una filosofía viva, no dogmática.

Botones CTA:

- Principal: `Explorar biblioteca` -> `/biblioteca`
- Secundario: `Quiénes somos` -> `/quienes-somos`

Notas:

- Esta franja debe ser breve y visual: 3 o 4 datos, no un bloque largo.
- Puede reutilizar `Tseyor en números`, pero con menos elementos arriba: `Libros`, `Comunicados`, `Audios`, `Voluntarios` o `Centros`.
- El contador completo puede quedar más abajo si se quiere mantener.

### 5. Elige cómo acercarte a TSEYOR

Tipo: Texto normal / tarjetas

Texto:

> Puedes empezar por el Curso Holístico, explorar libremente la Biblioteca Tseyor o conocer la vida de la comunidad a través del Blog y las Novedades.

Tarjetas:

- **Curso Holístico Tseyor**  
  Texto: Aprende con nuestro curso gratuito la base de la filosofía de las estrellas, de parte de nuestros Guías Estelares.  
  CTA: `Ver curso` -> `/cursos`

- **Biblioteca Tseyor**  
  Texto: Ofrecemos gratuitamente libros PDF, comunicados, meditaciones, audios, psicografías y materiales de libre descarga procedentes de la Fuente original del Fractal de Tseyor.  
  CTA: `Entrar en biblioteca` -> `/biblioteca`

- **Blog**  
  Texto: Aquí puedes conocer sobre la vida de la comunidad Tseyor, sus vivencias, actividades, encuentros e imágenes.  
  CTA: `Leer blog` -> `/blog`

- **Próximos eventos**  
  Texto: Consulta los próximos eventos de la comunidad Tseyor y encuentra rápidamente una actividad a la que puedas asistir.  
  CTA: `Ver eventos` -> `/eventos`  
  Condición: mostrar solo cuando haya eventos programados.

Notas:

- Esta sección corrige el problema de falta de CTAs intermedios.
- Debe aparecer muy arriba, justo después de la franja breve de confianza.
- Si hay eventos activos, esta tarjeta puede aparecer como cuarta opción. Si no hay eventos, se oculta para no crear una vía muerta.
- Diseño recomendado: tarjetas compactas, no grandes bloques. Cada tarjeta debe tener icono, título, una frase y un botón. En móvil, una por fila.

### 6. Curso Holístico Tseyor

Tipo: Texto normal con imagen

Texto:

> Aprende con nuestro curso gratuito la base de la filosofía de las estrellas, de parte de nuestros Guías Estelares.
>
> El curso ha sido desarrollado en base a las enseñanzas de nuestros Guías Estelares y funciona bajo el mecanismo de la retroalimentación que nos permite el aprendizaje mutuo, sin dogmas y respetando el proceso de cada persona.
>
> Al terminar el curso recibirás tu nombre simbólico.

Botones CTA:

- Principal: `Inscríbete gratis` -> `/inscripcion`
- Secundario: `Ver detalles del curso` -> `/cursos`

Notas:

- Esta sección debe tener más peso que Novedades, Blog y Boletín.
- Texto tomado de `Cursos/Index.vue`.
- Debe aparecer antes de `La Revolución de la Consciencia`, porque es el objetivo principal de conversión.

Imagen/diseño:

- Usar `/almacen/medios/paginas/curso.png` o una imagen de meditación/personas aprendiendo si existe una más humana.
- Presentar 4 beneficios debajo del texto: `gratuito`, `sin dogmas`, `con retroalimentación`, `nombre simbólico al terminar`.
- Repetir CTA de inscripción al final de la sección, no solo arriba.

### 7. Biblioteca Tseyor

Tipo: Hero o texto normal con imagen

Texto:

> La Biblioteca Tseyor es el resultado de más de 1800 diálogos telepáticos con seres humanos de la Confederación de Mundos Habitados de la Galaxia, transmitidos a través de Chac-Mool Puente.
>
> Estas conversaciones interdimensionales se han transcrito en textos que inspiran libros, monografías, vídeos y audios.
>
> Todos los materiales son de libre acceso y descarga. Han sido publicados y preservados para mantener la integridad del mensaje original.

Botones CTA:

- Principal: `Explorar biblioteca` -> `/biblioteca`
- Secundario: `Ver comunicados` -> `/comunicados`

Notas:

- Reutiliza el texto más preciso de `Biblioteca.vue`, mejor que el resumen de portada.
- Puede mostrar accesos directos a libros, comunicados, audios y meditaciones.
- Debe decir explícitamente `libre descarga`, no solo `gratuito`, porque esa es una promesa central.

Imagen/diseño:

- Usar `/almacen/medios/portada/biblioteca_tseyor_libros.jpg`.
- Mostrar accesos como chips o tarjetas pequeñas: `Comunicados`, `Libros`, `Audios`, `Meditaciones`.
- Esta sección debe transmitir gratuidad y abundancia, pero sin saturar con demasiadas categorías.

### 8. La Revolución de la Consciencia

Tipo: Hero o texto normal con imagen

Texto:

> La verdadera transformación se logra mediante la autoobservación, el desapego y el sacrificio por la humanidad.
>
> Con la alquimia de un pensamiento sublimado transmutamos el plomo de nuestra personalidad por el oro del espíritu.
>
> Te invitamos a compartir este viaje de autodescubrimiento todos juntos. Ya es hora de despertar del sueño de los sentidos.

Botones CTA:

- Principal: `Conocer la filosofía` -> `/filosofia`
- Secundario: `Leer más` -> `/libros/la-revolucion-de-la-consciencia`

Notas:

- Reutiliza la sección actual de portada.
- Debe actuar como puente hacia `/filosofia`, no solo como enlace a libro.
- Ubicar después de curso y biblioteca. Es potente, pero más conceptual.

### 9. Profundizar en los Guías Estelares

Tipo: Texto normal con imagen

Texto:

> Recibimos las referencias de nuestros tutores de la Confederación de Mundos Habitados de la Galaxia.
>
> Todos nuestros materiales se basan en comunicaciones mantenidas con nuestros tutores, quienes con paciencia, amor y comprensión nos guían en este proceso de cambio de Era.

Botones CTA:

- Principal: `Conocer a los Guías Estelares` -> `/guias`
- Secundario: `Leer comunicados` -> `/comunicados`

Notas:

- Esta sección pasa a ser de profundización porque el origen estelar ya debe aparecer en el hero y en `De dónde nace la filosofía Tseyor`.
- Puede eliminarse si la home queda demasiado larga, siempre que el bloque temprano de origen estelar exista.
- Si se mantiene, debe mostrar más detalle y no repetir exactamente el bloque temprano.

### 10. Comunidad Tseyor

Tipo: Hero o texto normal con imagen

Texto:

> Somos una comunidad conformada por miembros de la ONG Mundo Armónico TSEYOR, personas normales y corrientes que trabajan hacia la instauración de las Sociedades Armónicas de la Galaxia.
>
> Mundo Armónico Tseyor es una ONG dedicada al autodescubrimiento y a la creación de las Sociedades Armónicas en este planeta.

Botones CTA:

- Principal: `Quiénes somos` -> `/quienes-somos`
- Secundario: `Ver blog` -> `/blog`

Notas:

- El blog funciona aquí como prueba humana y visual.
- Usar imágenes reales del grupo cuando sea posible.
- Esta sección debe funcionar como transición natural hacia el Blog.
- Debe aclarar que la comunidad no es un foro abierto sin estructura, sino una comunidad formada por miembros de la ONG.

### 11. Testimonios

Tipo: Texto normal / carrusel o tarjetas

Texto:

> Personas que han compartido este camino cuentan cómo han vivido el autodescubrimiento, la retroalimentación, el Curso Holístico y la comunidad Tseyor desde una búsqueda sincera, libre y sin dogmas.

Botones CTA:

- Principal: `Comenzar el Curso Holístico` -> `/cursos`
- Secundario: `Leer blog` -> `/blog`

Notas:

- Colocar después de `Comunidad Tseyor` y antes de `Blog`.
- Motivo: primero se presenta la comunidad, luego se muestra prueba social en voz de personas reales, y después se ofrece el blog para ver fotos, relatos y vida grupal.
- No inventar testimonios. Usar citas reales, autorizadas y con nombre simbólico, iniciales o formato anónimo si la persona lo prefiere.
- Priorizar testimonios que reduzcan objeciones: "sin dogmas", "me sentí acompañado", "pude explorar a mi ritmo", "el curso me ayudó a ordenar la filosofía".

Diseño:

- 3 testimonios visibles en escritorio; 1 por pantalla en móvil.
- Cada tarjeta: frase breve, nombre o identificador, relación con TSEYOR (`alumna del curso`, `miembro de la comunidad`, `participante de encuentros`).
- Si hay foto real autorizada, usar avatar pequeño. Si no, no usar fotos genéricas.
- Evitar carruseles automáticos rápidos; mejor tarjetas estáticas o carrusel manual.

### 12. Blog: vida de la comunidad

Tipo: Texto normal / listado de artículos

Texto:

> Aquí puedes conocer sobre la vida de la comunidad Tseyor.
>
> Conoce algunas de nuestras aventuras en este camino del autorreconocimiento; podrás vernos en muchas fotografías de eventos que hemos realizado.

Botones CTA:

- Principal: `Leer blog` -> `/blog`
- Secundario: `Ver biblioteca` -> `/biblioteca`

Notas:

- Mostrar 3 entradas recientes con imagen.
- No debe competir con el curso; funciona como conexión emocional.
- Diseño recomendado: grid de 3 cards con imagen grande, título y fecha. Evitar mostrar mucho texto; aquí manda la imagen.

### 13. Novedades

Tipo: Texto normal / listado de novedades

Texto:

> Todas las novedades de los contenidos de Tseyor.

Botones CTA:

- Principal: `Ver novedades` -> `/novedades`
- Secundario: `Suscribirme al boletín` -> `/boletines`

Notas:

- Mostrar 3 o 4 novedades recientes.
- Novedades sirve para retorno y actualidad, no para explicar la esencia a visitantes nuevos.
- Puede ir después del blog, en formato compacto.

### 14. Suscríbete

Tipo: Texto normal / formulario

Texto:

> Recibe novedades, contenidos y publicaciones recientes de TSEYOR.

Botones CTA:

- Principal: `Suscribirme` -> acción del formulario
- Secundario: `Ver boletines anteriores` -> `/boletines`

Notas:

- Mantener el componente `Suscribe`.
- Reducir fricción: email como campo principal.

## `/quienes-somos`

Función de la página: generar confianza, explicar comunidad, origen, guías, experiencia y vida grupal. Debe llevar hacia curso, filosofía, guías, biblioteca y blog.

Orden optimizado:

1. Identidad: ONG sin ánimo de lucro y filosofía viva.
2. Comunidad de miembros y presencia territorial.
3. Guías Estelares y comunicados, como fundamento diferencial.
4. Contenidos libres y biblioteca, como prueba de acceso abierto.
5. Blog y fotografías, como prueba humana.
6. Testimonios, como prueba social cercana.
7. Preguntas frecuentes, para resolver objeciones.
8. CTA final hacia Curso Holístico.

Esta página debe responder a la duda "¿quiénes sois realmente?" antes de pedir inscripción. El tono debe ser cercano, transparente y no dogmático.

### 1. ¿Quiénes somos?

Tipo: Hero

Texto:

> Mundo Armónico Tseyor es una ONG dedicada al autodescubrimiento y a la creación de las Sociedades Armónicas en este planeta.
>
> Sus principios se basan en la experimentación de la filosofía cósmico-crística que nos traen nuestros Guías Estelares, con quienes mantenemos conversaciones regulares con la Confederación de Mundos Habitados de la Galaxia.
>
> Esta es una filosofía viva, no dogmática, y plena de vigor espiritual y creativo, en estos tiempos de transformación.
>
> Somos una ONG sin ánimo de lucro. Nuestra comunidad está conformada por miembros de Mundo Armónico TSEYOR y nuestras actividades se desarrollan desde el servicio, la ayuda humanitaria y el conocimiento libre.

Botones CTA:

- Principal: `Conoce nuestros orígenes` -> `/origenes-de-tseyor`
- Secundario: `Ver Curso Holístico` -> `/cursos`

Notas:

- Mantener la sección actual, pero añadir CTA secundario al curso.
- Imagen recomendada: `/almacen/medios/paginas/quienes-somos.jpg`, pero priorizar una composición donde se vea comunidad real si está disponible.
- Diseño: hero menos abstracto que la home. Aquí la confianza pesa más que el impacto visual.
- Añadir chips bajo el texto: `ONG sin ánimo de lucro`, `Sin dogmas`, `Comunidad de miembros`, `Conocimiento gratuito`.

### 2. ¿Dónde estamos?

Tipo: Texto normal con imagen

Texto:

> Encuentra un centro o representante de Mundo Armónico TSEYOR cerca de ti.
>
> Estamos creciendo. Si aún no hay uno en tu zona, ¡anímate a crear uno! Únete a nuestro proyecto y te acompañaremos en el camino.

Botones CTA:

- Principal: `Ver mapa` -> `/contactos`
- Secundario: `Quiero unirme` -> `/cursos`

Imagen/diseño:

- Usar `/almacen/medios/paginas/mapa.jpg`.
- Añadir una microfrase de confianza junto al mapa: `Si aún no hay un centro en tu zona, te acompañaremos en el camino.`
- En móvil, el mapa debe aparecer después del texto y antes de los botones.
- Indicar que centros y representantes forman parte de la estructura de la ONG o colaboran con ella.

### 3. ¿Quiénes son nuestros amigos del espacio?

Tipo: Texto normal con imagen

Texto:

> Nuestros tutores proceden de distintos planetas y todos son miembros de la Confederación de Mundos Habitados de la Galaxia.
>
> Actualmente nos comunicamos con Shilcars, un ser humano del planeta Agguniom en la Constelación de Áuriga, y otros hermanos como Melcor, Aumnor y Noiwanak.

Botones CTA:

- Principal: `Conoce a los Guías Estelares` -> `/guias`
- Secundario: `Ver comunicados` -> `/comunicados`

Imagen/diseño:

- Usar `/almacen/medios/paginas/todos-los-guias.jpg`.
- Esta sección puede generar escepticismo en visitantes nuevos; conviene presentarla después de identidad/comunidad y antes de comunicados, no en el primer impacto.

### 4. Nuestra filosofía

Tipo: Texto normal con imagen

Texto:

> Practicamos la filosofía que nos ha patrocinado la Confederación de Mundos Habitados de la Galaxia, basada en la unidad en la diversidad, el hermanamiento y el Conocimiento Trascendente.
>
> Esta filosofía práctica nos permite conocernos a nosotros mismos y transmutar lo necesario para lograr un equilibrio y una preparación para dar el Salto Cuántico con la llegada del Rayo Sincronizador.
>
> No se trata de creer por creer. Cada persona está invitada a experimentar, compartir, comprobar y extraer sus propias conclusiones.

Botones CTA:

- Principal: `Conoce nuestra filosofía` -> `/filosofia`
- Secundario: `Curso gratuito` -> `/cursos`

Notas:

- Esta sección debe ser el punto de `/quienes-somos` donde `sin dogmas` se explica con más claridad.
- La idea importante es unir filosofía viva con experiencia personal, no presentar un sistema cerrado de creencias.

### 5. Comunicados recibidos telepáticamente

Tipo: Texto normal con imagen

Texto:

> Los comunicados son las conversaciones interdimensionales realizadas telepáticamente por nuestro hermano Chac-Mool Puente, quien ha recibido una preparación desde hace más de 40 años para asumir dicha labor como intermediario entre los dos mundos.
>
> En nuestra biblioteca web podéis encontrar todos los comunicados o conversaciones telepáticas interdimensionales mantenidas con los hermanos de la Confederación de Mundos Habitados de la Galaxia.
>
> Estos son comunicados grupales para crear sociedades sin líderes, sin piramidalismo vertical, para impulsar la auténtica libertad del individuo, sabiéndose él mismo participante de una Comunidad Universal.
>
> Los comunicados, libros PDF, audios y materiales relacionados están disponibles para libre consulta y descarga.

Botones CTA:

- Principal: `Ver comunicados` -> `/comunicados`
- Secundario: `Explorar biblioteca` -> `/biblioteca`

Imagen/diseño:

- Usar `/almacen/medios/paginas/Puente.jpg`.
- Mostrar esta sección como fundamento documental: "más de 40 años", "biblioteca web", "conversaciones interdimensionales".
- Puede incluir enlaces secundarios a `Libros` y `Audios` si el diseño lo permite, pero sin cargar la sección.

### 6. Conocimiento gratuito y libre descarga

Tipo: Texto normal / tarjetas pequeñas

Texto:

> Creemos en el acceso libre y gratuito al conocimiento. Por eso hemos recopilado libros PDF, comunicados, audios, meditaciones y otros materiales en una biblioteca web de fácil acceso para todos.
>
> El acceso a estos materiales invita a la experimentación personal y a la reflexión libre, no a la aceptación dogmática.

Botones CTA:

- Principal: `Explorar biblioteca` -> `/biblioteca`
- Secundario: `Ver libros` -> `/libros`

Notas:

- Colocar después de comunicados y antes del blog.
- Motivo: primero se explica de dónde nacen los materiales; después se aclara que son libres y descargables.
- Puede mostrar accesos a `Libros`, `Comunicados`, `Audios` y `Meditaciones`.

### 7. Blog

Tipo: Texto normal con imagen / listado de entradas

Texto:

> Conoce algunas de nuestras aventuras en este camino del autorreconocimiento; podrás vernos en muchas fotografías de eventos que hemos realizado.

Botones CTA:

- Principal: `Leer blog` -> `/blog`
- Secundario: `Ver novedades` -> `/novedades`

Notas:

- Esta sección es importante para que la gente conecte rápidamente con el grupo.
- Puede mostrar entradas recientes con fotografía.
- Diseño recomendado: 3 entradas con fotografía, título y fecha. No usar texto largo.
- Posición recomendada: antes de FAQ. Primero conectar con personas reales, después resolver dudas.

### 8. Testimonios de la comunidad

Tipo: Texto normal / tarjetas

Texto:

> Personas de la comunidad comparten su experiencia en este camino de autodescubrimiento, hermanamiento y retroalimentación.
>
> Sus testimonios deben ayudar a mostrar una comunidad sincera, sin dogmas y abierta a que cada persona viva su propio proceso.

Botones CTA:

- Principal: `Ver Curso Holístico` -> `/cursos`
- Secundario: `Leer blog` -> `/blog`

Notas:

- Colocar después de `Blog` y antes de `Preguntas más frecuentes`.
- Motivo: el blog enseña vida y fotografías; los testimonios convierten esa cercanía en confianza verbal. Después, FAQ resuelve dudas concretas.
- Usar testimonios reales de miembros o participantes, no textos promocionales.
- Si hay testimonios sobre centros o encuentros presenciales, esta página es mejor lugar que `/cursos`.

Diseño:

- 3 tarjetas sencillas con frase, nombre/identificador y relación con la comunidad.
- Evitar exceso de solemnidad. Deben sonar humanos, cercanos y sinceros.

### 9. Preguntas más frecuentes

Tipo: Texto normal

Texto:

> En nuestra biblioteca web podéis encontrar todos los comunicados o conversaciones telepáticas interdimensionales mantenidas con los hermanos de la Confederación de Mundos Habitados de la Galaxia.
>
> Estas y otras muchas preguntas ordenadas por temática puedes consultarlas en el libro-documento.

Preguntas visibles:

- ¿TSEYOR es dogmático?
- ¿Cómo son los seres de otros planetas?
- ¿Cómo contactan?
- ¿Cómo viven?
- ¿Por qué están aquí?
- ¿Cómo son sus naves?
- ¿Cómo puedo comunicarme con ellos?
- ¿Por qué no arreglan los problemas de nuestro mundo?
- ¿Por qué no se presentan?

Botones CTA:

- Principal: `Ver preguntas frecuentes` -> `/preguntas-frecuentes`
- Secundario: `Ver biblioteca` -> `/biblioteca`

Diseño:

- Formato acordeón o dos columnas de enlaces.
- Mantener solo las preguntas que más anticipan objeciones de visitantes nuevos: dogma, contacto, propósito, comunicación, presencia.
- La pregunta `¿TSEYOR es dogmático?` debe responder en positivo y simple: `No. Proponemos experimentar, comprobar y compartir desde la libertad interior.`

### 10. ONG Mundo Armónico Tseyor

Tipo: Texto normal con imagen

Texto:

> Conoce nuestra ONG Mundo Armónico Tseyor, dedicada a la ayuda humanitaria en su vertiente material y espiritual.
>
> Todas sus actividades se desarrollan sin ánimo de lucro y desde la participación de sus miembros.

Botones CTA:

- Principal: `Conoce la ONG` -> `/ong`
- Secundario: `Ver contactos` -> `/contactos`

### 11. ¿Qué significa TSEYOR?

Tipo: Hero o texto normal destacado

Texto:

> Tseyor es un acrónimo que corresponde a:
>
> Tiempo Simbólico Estelar del YO en Retroalimentación.
>
> Y en él están incluidas miles de personas que anhelan el perfeccionamiento de su pensamiento.

Botones CTA:

- Principal: `Más información` -> `/libros/quienes-somos-en-tseyor`
- Secundario: `Curso Holístico` -> `/cursos`

### 12. Quiero ser de Tseyor

Tipo: Hero final

Texto:

> Para entrar a formar parte de la comunidad de miembros de la ONG debes realizar nuestro Curso Holístico, de forma totalmente gratuita.

Botones CTA:

- Principal: `Curso Holístico` -> `/cursos`
- Secundario: `Inscríbete` -> `/inscripcion`

Diseño:

- Esta debe ser una sección final clara, con fondo visual sencillo y un solo CTA dominante.
- En móvil, `Inscríbete` debe aparecer como botón principal; `Curso Holístico` puede quedar como enlace secundario si el usuario necesita más contexto.

## `/cursos`

Función de la página: conversión fuerte hacia inscripción. Debe explicar qué es el Curso Holístico, para quién es, qué contiene, cómo se aprende y qué ocurre al terminar.

Orden optimizado:

1. Hero con curso gratuito e inscripción.
2. Vídeo de presentación para reducir incertidumbre.
3. Qué vas a encontrar, en tarjetas.
4. Cómo se aprende: retroalimentación.
5. Testimonios de alumnos o participantes.
6. Libro del curso como material de apoyo.
7. Eventos o centros solo si aportan valor real.
8. CTA final de inscripción.

Esta página debe tener menos rutas laterales que las demás. El objetivo es que quien llegue aquí entienda rápido y se inscriba.

### 1. Curso Holístico Tseyor

Tipo: Hero

Texto:

> Aprende con nuestro curso gratuito la base de la filosofía de las estrellas, de parte de nuestros Guías Estelares.
>
> Al terminar el curso recibirás tu nombre simbólico.
>
> El curso se vive desde la retroalimentación y la experimentación personal, sin dogmas.

Botones CTA:

- Principal: `Inscríbete` -> `/inscripcion`
- Secundario: `Descargar libro del curso` -> `/libros/curso-holistico-tseyor`

Notas:

- Esta sección ya existe; conviene elevarla a Hero real.
- Título recomendado: mantener `Curso Holístico Tseyor`.
- Subtítulo recomendado desde el texto actual: `Aprende con nuestro curso gratuito la base de la filosofía de las estrellas, de parte de nuestros Guías Estelares.`
- El botón principal debe decir `Inscríbete gratis`, no solo `Inscríbete`, para reducir fricción.

Imagen/diseño:

- Usar `/almacen/medios/paginas/curso.png` o una imagen más cálida si existe.
- Añadir una fila de 4 señales junto al CTA: `Gratuito`, `Sin dogmas`, `Con acompañamiento`, `Nombre simbólico al terminar`.

### 2. Vídeo de presentación

Tipo: Texto normal / vídeo

Texto:

> Mira la presentación del Curso Holístico Tseyor y conoce su enfoque antes de inscribirte.
>
> El vídeo debe ayudar a entender que el curso es gratuito, acompañado y sin dogmas.

Botones CTA:

- Principal: `Inscribirme gratis` -> `/inscripcion`

Notas:

- Mantener el vídeo actual `https://www.youtube.com/embed/AkOZbfGdXbU`.
- El vídeo no debe ocupar el primer pantallazo por encima del CTA. Debe ir después del hero.
- Añadir CTA debajo del vídeo porque quien termina de verlo está en mejor momento para inscribirse.
- Añadir debajo del vídeo una frase de garantía: `El curso acompaña tu exploración; no impone creencias.`

### 3. Qué vas a encontrar

Tipo: Texto normal / tarjetas

Texto:

> El curso reúne exploración cósmica, desarrollo personal, extrapolación mental, cuántica y conciencia.
>
> Cada tema se trabaja desde la retroalimentación y la propia comprobación.

Tarjetas:

- **Exploración Cósmica**  
  Texto: Conoce el papel de la Confederación de Mundos Habitados de la Galaxia.

- **Desarrollo personal**  
  Texto: Descubre herramientas prácticas para el desarrollo interior.

- **Extrapolación mental**  
  Texto: Expande tu comprensión de la existencia de otros mundos.

- **Cuántica y Conciencia**  
  Texto: Explora las posibilidades de un próximo salto cuántico.

Botones CTA:

- Principal: `Empezar el curso` -> `/inscripcion`
- Secundario: `Conocer la filosofía` -> `/filosofia`

Diseño:

- Usar `FeatureColumns` como ahora, pero con CTA debajo de las tarjetas.
- Mantener las cuatro tarjetas: son claras y escaneables.
- Evitar añadir más de cuatro puntos para no diluir el foco.
- Añadir un sello visual `Sin dogmas` junto a `Gratuito` en esta sección si el hero queda fuera de vista al hacer scroll.

### 4. Filosofía Cósmico-Crística

Tipo: Texto normal con imagen

Texto:

> El curso ha sido desarrollado en base a las enseñanzas de nuestros Guías Estelares.
>
> Funciona bajo el mecanismo de la retroalimentación que nos permite el aprendizaje mutuo, sin dogmas y respetando la propia comprobación.

Botones CTA:

- Principal: `Conoce nuestra filosofía` -> `/filosofia`
- Secundario: `Inscríbete` -> `/inscripcion`

Diseño:

- Esta sección debe explicar método, no abrir demasiada teoría.
- Cambiar jerarquía visual: el CTA de inscripción debe seguir presente, aunque el CTA conceptual sea filosofía.

### 5. Testimonios del Curso Holístico

Tipo: Texto normal / tarjetas

Texto:

> Personas que han realizado el Curso Holístico comparten cómo vivieron el aprendizaje, la retroalimentación y su acercamiento a la filosofía Tseyor.
>
> Deben mostrar que el curso acompaña sin imponer, desde la libertad interior y la comprobación personal.

Botones CTA:

- Principal: `Inscribirme gratis` -> `/inscripcion`
- Secundario: `Descargar libro del curso` -> `/libros/curso-holistico-tseyor`

Notas:

- Colocar después de explicar qué contiene el curso y cómo funciona la retroalimentación, antes del libro.
- Motivo: en una página de conversión, los testimonios funcionan mejor cuando la persona ya entiende la propuesta y necesita confianza para decidir.
- Usar testimonios reales de alumnos o personas que hayan completado el curso. No usar frases genéricas ni inventadas.
- Seleccionar testimonios concretos: qué duda tenían antes, qué descubrieron durante el curso, qué cambió al terminar.
- Al menos uno debe reforzar explícitamente que el curso se sintió libre, respetuoso y sin dogmas.

Diseño:

- 2 o 3 testimonios breves, máximo 40-60 palabras cada uno.
- Añadir etiqueta de contexto: `Curso Holístico`, `Nombre simbólico recibido`, `Participante online`, etc.
- Si hay vídeo-testimonio real, uno solo puede reemplazar una tarjeta, pero no debe empujar el CTA fuera de la vista.

### 6. Libro del Curso

Tipo: Texto normal con imagen / libro 3D

Texto:

> Cuentos de nuestros Guías Estelares con enseñanzas filosóficas que ayudan enormemente a comprender la filosofía de Tseyor.

Botones CTA:

- Principal: `Descargar libro` -> `/libros/curso-holistico-tseyor`
- Secundario: `Inscribirme al curso` -> `/inscripcion`

Diseño:

- El `Libro3d` funciona bien como elemento visual.
- Añadir texto pequeño: `Puedes leer el material antes o durante el curso.`
- Si el usuario descarga el libro, mantener cerca un CTA de inscripción para no convertir esta sección en salida lateral.

### 7. Próximos cursos y eventos

Tipo: Texto normal / listado

Texto:

> Consulta regularmente los Eventos para estar informado de los próximos eventos de la comunidad Tseyor.
>
> También puedes acercarte al centro Tseyor más cercano y solicitar un curso para ti.

Botones CTA:

- Principal: `Ver eventos` -> `/eventos`
- Secundario: `Ver mapa` -> `/contactos`

Notas:

- Esta sección está desactivada actualmente con `v-if="false"`.
- Reactivarla solo si hay datos útiles; si no, no debe distraer de la inscripción.
- Si hay eventos relacionados con cursos, mostrarlos. Si son eventos generales, mejor enlazarlos desde home, no desde `/cursos`.
- En esta página, eventos debe ser soporte, no objetivo.

### 8. Inscripción final

Tipo: Hero final

Texto:

> Te invitamos a recorrer con nosotros el camino del autodescubrimiento con plena libertad.

Botones CTA:

- Principal: `Inscribirme gratuitamente` -> `/inscripcion`
- Secundario: `Explorar biblioteca antes` -> `/biblioteca`

Diseño:

- Sección final simple, sin demasiadas alternativas.
- En móvil, solo mostrar el botón principal grande y el secundario como enlace discreto.

## `/filosofia`

Función de la página: profundizar en el marco espiritual de TSEYOR y devolver hacia curso, biblioteca, glosario y materiales. Debe sostener la intensidad conceptual sin perder próximos pasos.

Orden optimizado:

1. Tiempos de transformación.
2. Interesa la comprobación.
3. Proceso de autodescubrimiento.
4. Una puerta abierta al infinito.
5. Filosofía cuántica.
6. Sociedades Armónicas.
7. Glosario y materiales.
8. CTA final hacia Curso Holístico.

El cambio más importante es subir `Interesa la comprobación`: para el público que busca espiritualidad sin dogmas, esa sección funciona como promesa de libertad y criterio propio.

### 1. Tiempos de transformación

Tipo: Hero

Texto:

> Nos encontramos en un momento de transformación sin precedentes a nivel cósmico, que nos va a conducir a un nuevo nivel vibratorio, lo que significa mayor comprensión.
>
> La Confederación de Mundos Habitados de la Galaxia está tutelando al grupo Tseyor en un proceso de autodescubrimiento.
>
> Un proceso que llevará a la humanidad a conectarse con sus otras realidades interdimensionales.
>
> Este proceso se propone desde una filosofía viva, no dogmática, abierta a la propia comprobación.

Botones CTA:

- Principal: `Curso gratuito` -> `/cursos`
- Secundario: `Explorar biblioteca` -> `/biblioteca`

Notas:

- Añadir CTA al Hero actual.
- Mantener esta sección como apertura conceptual, pero no alargarla. Debe invitar a profundizar, no explicarlo todo.
- Aunque `Interesa la comprobación` va después, el hero de `/filosofia` ya debe anticipar que el recorrido no es dogmático.

Imagen/diseño:

- Usar `/almacen/medios/paginas/hombre-universo.jpg`.
- El hero debe ser contemplativo, pero con botones claros. Actualmente la página empieza fuerte en tono, pero débil en acción.

### 2. Interesa la comprobación

Tipo: Texto normal con imagen

Texto:

> No habremos de creer nada; en lugar de eso comprobaremos mediante la experimentación.
>
> Solo la propia comprobación nos dará la convicción y la certeza de que estamos en una transformación de nuestras impresiones, contemplando las realidades que están aquí y ahora.

Botones CTA:

- Principal: `Leer filosofía` -> `/libros/filosofia`
- Secundario: `Empezar el curso` -> `/cursos`

Notas:

- Esta sección está comentada en `Filosofia.vue`, pero debe recuperarse.
- Ubicación recomendada: justo después del hero, porque reduce la objeción "esto suena dogmático".

Imagen/diseño:

- Usar `/almacen/medios/paginas/hombre-con-lupa.jpg` si existe y está disponible.
- Diseño sobrio, con énfasis en la frase `No habremos de creer nada`.

### 3. El proceso de autodescubrimiento

Tipo: Texto normal con imagen

Texto:

> A través de meditaciones, talleres y un gran sentimiento de hermanamiento conseguimos transmutar nuestra personalidad hasta lograr la Unidad.

Botones CTA:

- Principal: `Curso gratuito` -> `/cursos`
- Secundario: `Ver meditaciones` -> `/meditaciones`

Diseño:

- Usar `/almacen/medios/portada/meditando.jpg`.
- Esta sección debe hacer tangible la filosofía: prácticas, talleres, meditación, hermanamiento.

### 4. Una puerta abierta al infinito

Tipo: Hero o texto normal con imagen

Texto:

> Aprendamos a conectar con nuestra chispa divina, con la inestimable ayuda del Cristo-Cósmico, a través de nuestro amigo el pequeño Christian.
>
> Y descubriremos en nosotros nuestro universo interior, hallando la verdadera felicidad y la gran Verdad que siempre estuvo ahí.

Botones CTA:

- Principal: `Leer más en biblioteca` -> `/biblioteca`
- Secundario: `Ver glosario` -> `/glosario`

Notas:

- Mantener después de autodescubrimiento. Así la página pasa de criterio propio a práctica, y luego a profundidad espiritual.

### 5. Filosofía cuántica

Tipo: Texto normal con imagen

Texto:

> La realidad cuántica significa creación a través del pensamiento.
>
> Necesitamos claves, referencias, dispositivos, que nos permitan ese lanzamiento hacia las estrellas, que en el fondo no es más que el reencuentro con uno mismo, a través de uno mismo en su universo interior.

Botones CTA:

- Principal: `Leer libro de filosofía` -> `/libros/filosofia`
- Secundario: `Consultar glosario` -> `/glosario`

Diseño:

- Usar `/almacen/medios/paginas/hombre-comprension.jpg`.
- Evitar sobrecargar con explicaciones largas; esta sección debe abrir una puerta y mandar al libro/glosario.

### 6. Las Sociedades Armónicas

Tipo: Texto normal con imagen

Texto:

> Son sociedades en las que predomina la hermandad, no hay líderes, no hay enfermedad, y sí la auténtica libertad.
>
> A nuestro planeta le ha llegado el momento de transformarse de forma pacífica y con mucho amor y paciencia materializar las Sociedades Armónicas.

Botones CTA:

- Principal: `Ver en Biblioteca` -> `/libros/las-sociedades-armonicas`
- Secundario: `Quiénes somos` -> `/quienes-somos`

Diseño:

- Usar `/almacen/medios/paginas/SociedadesArmonicas.jpg`.
- Esta sección puede incluir una mini lista de 3 ideas: `hermandad`, `auténtica libertad`, `sin líderes`.

### 7. Glosario

Tipo: Texto normal con imagen

Texto:

> Consulta todos los términos de la filosofía Tseyor en nuestro glosario de términos. También podrás ver los Guías Estelares y las preguntas frecuentes.

Botones CTA:

- Principal: `Ir al Glosario` -> `/glosario`
- Secundario: `Ver preguntas frecuentes` -> `/preguntas-frecuentes`

Diseño:

- Presentar como ayuda para orientarse, no como sección final pesada.
- Añadir enlaces rápidos a `Guías Estelares` y `Preguntas frecuentes` si caben como chips.

### 8. Únete a Tseyor

Tipo: Hero final

Texto:

> Te invitamos a recorrer con nosotros el camino del autodescubrimiento con plena libertad.
>
> Juntos, exploraremos nuestra esencia y buscaremos trascender las limitaciones de esta realidad ilusoria.
>
> Puedes empezar sin dogmas, desde tu propia experiencia y en retroalimentación con la comunidad.

Botones CTA:

- Principal: `Formulario de inscripción` -> `/inscripcion`
- Secundario: `Ver Curso Holístico` -> `/cursos`

Diseño:

- Cambiar el texto del botón principal a `Inscribirme al Curso Holístico` si se implementa directamente en UI.
- Fondo visual: `/almacen/medios/paginas/inscribirse.jpg`.

## Reglas de navegación y jerarquía

- El botón más visible de la home debe apuntar a `/cursos` o `/inscripcion`.
- La home debe comunicar en los primeros 30 segundos que la filosofía Tseyor proviene de los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia. Este es el diferencial principal frente a otras filosofías espirituales o de crecimiento personal.
- La condición institucional debe aparecer arriba: ONG sin ánimo de lucro, actividades no lucrativas, comunidad de miembros y contenidos libres.
- `/biblioteca` debe aparecer como segunda vía fuerte, especialmente para visitantes que quieren investigar antes de inscribirse.
- `/biblioteca` debe comunicar explícitamente `libre descarga`, especialmente para libros PDF y audios.
- `Sin dogmas` debe aparecer arriba y repetirse en los lugares donde más reduce fricción: home, `/quienes-somos`, `/cursos`, `/filosofia` y testimonios.
- `Próximos eventos` debe aparecer en el primer viewport de la home cuando existan eventos programados, como CTA contextual de asistencia. Es más urgente que Blog/Novedades, pero no debe desplazar el CTA principal del curso.
- `/blog` debe estar visible en home y en `/quienes-somos`, porque aporta cercanía, fotografías y vida comunitaria.
- `/novedades` debe aparecer como actualidad y retorno, no como explicación principal de TSEYOR.
- El boletín debe aparecer en home y en novedades como continuidad de bajo compromiso.
- `/filosofia` debe incluir CTAs hacia `/cursos`, `/biblioteca`, `/glosario` y libros concretos.
- `/quienes-somos` debe terminar siempre en curso/inscripción, porque quien llega al final ya tiene suficiente contexto.

## Diseño global de componentes

### Hero

- Estructura: sobrelínea pequeña, H1 claro, texto de 2-3 líneas, CTA principal, CTA secundario y CTA condicional si aplica.
- El H1 debe hablar de resultado o esencia, no solo del nombre institucional. En home debe incluir o insinuar claramente `filosofía de las estrellas`.
- En home, evitar textos largos dentro del hero. El detalle va después.
- En móvil, botones apilados: principal primero, secundario después, eventos debajo si existe.
- En tablet, el hero no debe copiar literalmente ni el escritorio ni el móvil: mantener lectura cómoda, CTAs en línea cuando quepan y composición más contenida.
- La sobrelínea o sello del hero debe poder mostrar `ONG sin ánimo de lucro` sin robar protagonismo al H1.

### Sellos de Confianza

- Usar sellos/chips breves en home y `/quienes-somos`: `ONG sin ánimo de lucro`, `Libre descarga`, `Curso gratuito`, `Comunidad de miembros`.
- Añadir `Sin dogmas` como sello estable junto a las garantías institucionales.
- Deben aparecer visualmente cerca del inicio, antes de que el usuario llegue a secciones largas.
- No tratarlos como decoración: cada sello debe corresponder a una promesa real del contenido.
- En páginas largas, repetir `Sin dogmas` cuando el usuario entra en una decisión: antes de inscripción, antes de descargar materiales o antes de leer filosofía profunda.

### Botones

- CTA principal: color de máximo contraste, texto con acción clara.
- CTA secundario: estilo outline o ghost, menos peso visual.
- CTA condicional `Próximos eventos`: estilo secundario destacado; visible, pero no más dominante que curso.
- Evitar `Más información` cuando se pueda usar un texto específico: `Leer sobre Sociedades Armónicas`, `Ver comunicados`, `Descargar libro`.
- En tablet, evitar CTAs de ancho completo salvo formularios o barras muy estrechas. Un botón de 100% de ancho en tablet suele parecer móvil ampliado y reduce percepción de cuidado.

### Tarjetas

- Usar tarjetas solo para opciones reales de elección: curso, biblioteca, blog, eventos, categorías.
- Cada tarjeta: icono, título, una frase, CTA. No meter párrafos largos.
- No anidar tarjetas dentro de tarjetas.

### Testimonios

- Ubicación principal en home: después de `Comunidad Tseyor` y antes de `Blog`.
- Ubicación principal en `/cursos`: después de explicar el contenido y la retroalimentación, antes de `Libro del Curso`.
- Ubicación opcional en `/quienes-somos`: después de `Blog` y antes de FAQ.
- No inventar testimonios. Deben ser reales, autorizados y revisados.
- Mejor 3 testimonios concretos que 10 frases vagas.
- Cada testimonio debe responder a una objeción o deseo: confianza, acompañamiento, libertad, claridad, conexión con personas afines.
- Evitar lenguaje demasiado publicitario. Deben sonar como personas reales, no como anuncios.

### Imágenes

- Home: priorizar imágenes que muestren resultado humano, comunidad o experiencia real.
- Quiénes somos: fotos de grupo, mapas, guías, encuentros y blog.
- Cursos: imagen clara del curso, libro 3D, vídeo y meditación.
- Filosofía: imágenes contemplativas, pero acompañadas de CTAs concretos.
- Blog: imágenes grandes y reales. Es la sección donde más conviene que la imagen hable.

### Branding Visual

- Base cromática: azul y blanco.
- Azul cielo: usarlo para comunicar extrapolación, conexión, apertura y dimensión cósmica.
- Blanco: usarlo para serenidad, paz interior, intuición y claridad.
- Elementos visuales: conexión, comunicación, unión entre dimensiones y elementos cósmicos o estelares de forma sutil.
- Evitar que el rediseño se vuelva oscuro en exceso. Puede haber secciones espaciales, pero la marca debe sentirse serena, luminosa y acogedora.

### Textos Institucionales

- Usar `ONG sin ánimo de lucro`, no solo `ONG`.
- Decir `contenidos de libre descarga` cuando se hable de libros PDF y audios.
- Decir `comunidad conformada por miembros de la ONG` cuando se explique pertenencia.
- Decir `sin dogmas` con sentido concreto: experimentación personal, retroalimentación, libertad interior y propia comprobación.
- Evitar que el usuario pueda interpretar que el curso o los materiales son productos comerciales.

### Textos Diferenciales y SEO

- Home debe incluir términos escaneables y rastreables: `filosofía de las estrellas`, `Guías Estelares`, `Confederación de Mundos Habitados de la Galaxia`, `comunicados interdimensionales`.
- No relegar `Guías Estelares` a una sección baja. Debe estar en hero, bloque temprano y CTAs.
- Evitar titulares genéricos como única entrada: `despertar espiritual`, `crecimiento interior` o `autoconocimiento` no bastan por sí solos porque no diferencian a TSEYOR.
- El tono debe distinguir sin exagerar: origen estelar claro, pero expresado con serenidad, honestidad y sin sensacionalismo.

### FAQ y Objeciones

- Añadir una pregunta visible: `¿TSEYOR es dogmático?`
- Respuesta base: `No. TSEYOR propone experimentar, comprobar, compartir y extraer cada uno sus propias conclusiones desde la libertad interior.`
- Esta objeción debe resolverse en `/quienes-somos`, `/filosofia` y, si el curso tiene FAQ propia, también en `/cursos`.
- No esconder esta respuesta en textos largos: debe ser escaneable.

### Listados dinámicos

- Blog en home: 3 artículos recientes con imagen, título y fecha.
- Novedades en home: 3 o 4 contenidos recientes con etiqueta de colección.
- Eventos en home: solo si hay eventos programados. Si no hay eventos, no mostrar bloque vacío ni CTA.
- Boletín: formulario simple, idealmente email y nombre opcional.

### Tablet

La tablet debe tratarse como un breakpoint propio, no como una versión estirada del móvil ni como escritorio comprimido. Es el punto donde más fácil se rompe la jerarquía: hay suficiente ancho para componer, pero no tanto como para usar layouts densos de escritorio.

Rango orientativo:

- Tablet vertical: 768-899 px.
- Tablet horizontal / pequeño portátil táctil: 900-1199 px.

Reglas generales:

- Usar contenedores más estrechos que en escritorio. Recomendación: `max-width` de lectura entre 680 y 860 px para textos largos, y hasta 1040 px para grids.
- Evitar secciones con dos columnas rígidas si una de ellas queda demasiado estrecha. En tablet vertical, preferir una columna con imagen arriba o intercalada.
- En tablet horizontal, se pueden usar dos columnas en hero y secciones imagen/texto, pero con proporciones tranquilas: texto 55-60%, imagen 40-45%.
- No usar botones de ancho completo por defecto. Reservar el ancho completo para móvil real o para formularios donde el botón actúa como cierre de una entrada.
- Mantener tap targets cómodos: mínimo 44-48 px de alto, pero con ancho natural según texto y padding, no `width: 100%`.
- Evitar cards enormes de una por fila si el contenido es breve. En tablet, muchas tarjetas funcionan mejor en grid de 2 columnas.
- Evitar también grids de 4 columnas heredados de escritorio: en tablet suelen comprimir demasiado texto, iconos y CTAs.

Hero en tablet:

- Tablet vertical: hero en una columna, pero con CTAs en fila si caben: `Comenzar el Curso Holístico`, `Explorar la biblioteca` y debajo `Próximos eventos` solo si existe.
- Tablet horizontal: hero en dos columnas si la imagen aporta valor real. Si la imagen es decorativa o espacial, mejor mantener una columna fuerte de texto y una franja visual secundaria.
- H1 recomendado: entre 40 y 52 px, evitando escalarlo por viewport. Debe ocupar 2-3 líneas como máximo.
- Texto del hero: 2 párrafos cortos o 1 párrafo más sellos. Si se siente largo en tablet, priorizar origen estelar, sin dogmas y ONG, y mover el resto a la franja institucional.
- Los sellos `ONG sin ánimo de lucro`, `Sin dogmas`, `Libre descarga`, `Comunidad de miembros` pueden ir en dos filas, no comprimidos en una sola.

CTAs en tablet:

- Grupo de CTAs recomendado: fila flexible con botones de ancho natural y `flex-wrap`.
- Ancho recomendado de botón principal: mínimo 220 px, máximo 320 px.
- Ancho recomendado de botón secundario: mínimo 190 px, máximo 280 px.
- `Próximos eventos` debe verse como acción contextual, no como tercer botón gigante. En tablet puede aparecer como botón secundario compacto junto a los otros CTAs o como enlace destacado debajo.
- Si tres botones no caben con aire, usar dos arriba y `Próximos eventos` debajo. No forzar los tres en una fila comprimida.
- Evitar que el CTA principal ocupe todo el ancho de una tablet de 768 px; visualmente parece una decisión móvil y resta sofisticación.

Tarjetas y caminos de entrada:

- En tablet vertical, `Curso`, `Biblioteca`, `Blog` y `Eventos` deben ir en grid 2x2 cuando haya eventos, o 2+1 centrado cuando solo haya tres tarjetas.
- En tablet horizontal, mantener grid de 4 solo si cada tarjeta conserva buena lectura, icono, frase y CTA sin saltos feos. Si no, usar 2x2.
- Las tarjetas deben tener altura estable, pero no forzar textos largos para igualarlas. Mejor limitar descripción a una frase.
- El botón dentro de tarjeta no debe ocupar todo el ancho salvo que la tarjeta sea muy estrecha. Preferir botón compacto alineado abajo.

Listados en tablet:

- Blog: 2 columnas con imagen visible. No reducir la imagen hasta que pierda capacidad emocional.
- Novedades: 2 columnas o lista compacta con etiqueta y fecha, según densidad.
- Testimonios: 2 columnas en tablet horizontal; carrusel o una columna escaneable en tablet vertical si los textos son largos.
- Biblioteca / categorías: grid de 2 columnas en tablet vertical y 3 columnas en tablet horizontal si los títulos no se parten mal.

Navegación en tablet:

- No asumir automáticamente menú hamburguesa. En tablet horizontal puede mantenerse navegación principal reducida si caben los enlaces esenciales.
- Si se usa navegación colapsada, el CTA principal debe quedar visible fuera del menú cuando sea posible: `Curso Holístico` o `Inscríbete gratis`.
- Evitar barras superiores con demasiados enlaces pequeños. Priorizar: `Curso`, `Biblioteca`, `Quiénes somos`, `Filosofía`, `Blog`.

Regla de decisión:

- Si un bloque en tablet parece una pantalla móvil agrandada, revisar ancho de CTAs, tamaño de imagen y número de columnas.
- Si un bloque parece escritorio apretado, revisar densidad de columnas, longitud de textos y tamaño de tarjetas.
- La tablet debe sentirse intencional: aire suficiente, botones compactos, jerarquía clara y lectura cómoda.

### Móvil

- La home debe mostrar en el primer viewport: esencia, CTA curso, CTA biblioteca y eventos si existen.
- La home debe mostrar o insinuar en el primer viewport: `filosofía de las estrellas` y `Guías Estelares`.
- En móvil real, los botones deben tener altura mínima cómoda y ancho completo cuando estén en columna. Esta regla no debe heredarse automáticamente en tablet.
- Evitar secciones full-screen consecutivas si esconden demasiado las rutas de acción.
- Las tarjetas de caminos deben aparecer pronto y ser fáciles de tocar.

## SEO de Home

Objetivo SEO principal: consolidar la entidad oficial `Mundo Armónico TSEYOR` y dejar claro el diferencial `filosofía de las estrellas`.

Title recomendado:

> Mundo Armónico TSEYOR | Filosofía de las estrellas sin dogmas

Meta description recomendada:

> ONG sin ánimo de lucro. Filosofía de las estrellas recibida de los Guías Estelares, Curso Holístico gratuito, biblioteca de libre descarga y comunidad TSEYOR.

H1 recomendado:

> Filosofía de las estrellas, conciencia sin dogmas

H2 prioritarios:

- `De dónde nace la filosofía TSEYOR`
- `ONG sin ánimo de lucro y conocimiento libre`
- `Curso Holístico TSEYOR`
- `Biblioteca TSEYOR de libre descarga`
- `Comunidad TSEYOR`

Términos que deben aparecer en los primeros bloques:

- `Mundo Armónico TSEYOR`
- `filosofía de las estrellas`
- `Guías Estelares`
- `Confederación de Mundos Habitados de la Galaxia`
- `ONG sin ánimo de lucro`
- `sin dogmas`
- `libre descarga`
- `Curso Holístico`

Schema recomendado:

- `Organization` para Mundo Armónico TSEYOR.
- `WebSite` en layout raíz o home.
- `WebPage` para la portada.
- `SearchAction` si se quiere habilitar sitelinks searchbox.

Nota: no cargar la home de palabras clave de forma artificial. Los términos deben aparecer porque explican la marca y orientan al usuario.

## Checklist Homepage

Prueba de 5 segundos:

- ¿Se entiende que TSEYOR no es una filosofía espiritual genérica?
- ¿Aparece el origen estelar de la filosofía?
- ¿Se entiende que es una ONG sin ánimo de lucro?
- ¿Se ve un CTA principal hacia el Curso Holístico?
- ¿Hay una vía secundaria clara hacia Biblioteca?

Prueba de 30 segundos:

- ¿Se entiende que la filosofía proviene de los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia?
- ¿Se entiende que no hay dogmas?
- ¿Se entiende que los contenidos son gratuitos o de libre descarga?
- ¿Se entiende que la comunidad está conformada por miembros de la ONG?
- ¿Hay caminos claros para empezar: curso, biblioteca, blog, eventos, boletín?

Checklist de conversión:

- CTA principal visible sin scroll: `Comenzar el Curso Holístico`.
- CTA secundario visible: `Explorar la biblioteca`.
- CTA condicional de eventos visible solo si hay eventos.
- Sellos visibles: `ONG sin ánimo de lucro`, `Sin dogmas`, `Libre descarga`, `Comunidad de miembros`.
- Prueba social visible antes del final: testimonios, números o vida comunitaria.
- Blog visible con imágenes reales.
- Boletín al final para usuarios no preparados para inscribirse.

## Cambios recomendados respecto al estado actual

- Home: reemplazar el CTA principal `Quiénes somos` por `Comenzar el Curso Holístico`.
- Home: cambiar el enfoque del hero para que no sea solo `despertar a una nueva consciencia`; debe incluir el diferencial `filosofía de las estrellas`, `Guías Estelares` y `Confederación`.
- Home: añadir una franja institucional inmediatamente después del hero: ONG sin ánimo de lucro, libre descarga, comunidad de miembros.
- Home: añadir `sin dogmas` en hero, sellos y franja institucional.
- Home: añadir la sección temprana `De dónde nace la filosofía Tseyor` antes de caminos, curso y biblioteca.
- Home: añadir una franja breve de confianza después del hero.
- Home: añadir una sección temprana de caminos: curso, biblioteca, blog y eventos condicionales.
- Home: mantener `Próximos eventos` como botón condicional en el primer viewport cuando `hayProximosEventos` sea verdadero; idealmente como CTA auxiliar flotante o bajo los CTAs principales, no como sustituto de curso/biblioteca.
- Home: si hay eventos programados, añadir una cuarta tarjeta condicional `Próximos eventos` en la sección temprana de caminos.
- Home: incorporar el curso de forma explícita; actualmente el curso no tiene suficiente presencia en la portada.
- Home: mover `La Revolución de la Consciencia` después de Curso y Biblioteca para que no retrase la conversión principal ni oculte el diferencial estelar.
- Home: añadir `Testimonios` después de `Comunidad Tseyor` y antes de `Blog`, porque ahí validan la comunidad antes de mostrar relatos e imágenes.
- Home: mantener `Tseyor en números`, pero como franja breve de confianza arriba o bloque completo después de curso/biblioteca/comunidad.
- Quienes somos: cambiar enlaces antiguos `/entradas` por `/blog`.
- Quienes somos: reforzar desde el hero que Mundo Armónico TSEYOR es una ONG sin ánimo de lucro y que la comunidad está conformada por miembros.
- Quienes somos: reforzar `filosofía viva, no dogmática` también como sello visible.
- Quienes somos: añadir sección `Conocimiento gratuito y libre descarga` después de comunicados.
- Quienes somos: añadir CTA secundario al curso en las secciones principales.
- Quienes somos: añadir testimonios de comunidad después del blog y antes de preguntas frecuentes.
- Cursos: mantener el contenido actual, pero convertirlo en embudo claro hacia `/inscripcion`.
- Cursos: añadir `sin dogmas` como garantía junto a gratuito, acompañamiento y retroalimentación.
- Cursos: añadir testimonios del curso después de explicar el contenido y la retroalimentación, antes del libro.
- Filosofía: recuperar y subir la sección comentada `Interesa la comprobación`, porque refuerza "sin dogmas".
- Filosofía: añadir CTAs a las secciones que ahora solo informan.

## Validación contra `branding.htm`

El rediseño cumple los puntos centrales del archivo de branding:

- **Nombre y naturaleza**: se refuerza `Mundo Armónico TSEYOR` como ONG y se añade explícitamente `sin ánimo de lucro`, por requisito de rediseño.
- **Eslogan**: `Encaminándonos hacia las Sociedades Armónicas` queda reflejado en hero, comunidad, filosofía y Sociedades Armónicas.
- **Valores clave**: espiritualidad, ayuda humanitaria, contacto extraterrestre y conocimiento gratuito aparecen en home, `/quienes-somos`, biblioteca y filosofía.
- **Diferencial estelar**: la home ahora debe comunicar en el hero y en una sección temprana que la filosofía Tseyor proviene de los Guías Estelares de la Confederación de Mundos Habitados de la Galaxia.
- **Filosofía viva, no dogmática**: el rediseño explicita `sin dogmas` como promesa transversal y lo vincula a experimentación personal, retroalimentación y propia comprobación.
- **Conocimiento gratuito**: el rediseño ahora dice de forma explícita `libre acceso`, `libre descarga`, `libros PDF` y `audios`.
- **Biblioteca web**: queda como CTA secundario global y sección fuerte en home; también se refuerza en `/quienes-somos`.
- **Más de 40 años de experiencia**: aparece en la franja de confianza y en comunicados.
- **Guías Estelares / Confederación de Mundos Habitados de la Galaxia**: presentes en home, `/quienes-somos`, `/cursos` y `/filosofia`; en home pasan a ser mensaje de primer nivel, no una sección secundaria.
- **Experiencias interdimensionales, avistamientos y encuentros**: quedan representadas en home, blog, comunidad y `/quienes-somos`.
- **Público objetivo**: el recorrido habla a personas con inquietud espiritual, mente abierta, deseo de autodescubrimiento, solidaridad y búsqueda de personas afines.
- **Personalidad de marca**: el rediseño conserva una voz inspiradora, cercana, auténtica, transparente, trascendente, respetuosa, empoderadora, humilde, visionaria y abierta.
- **Visual branding**: se añaden reglas para azul/blanco, serenidad, conexión, unión entre dimensiones y elementos cósmicos sutiles.

Puntos reforzados respecto al branding original:

- El branding habla de ONG, ayuda humanitaria y conocimiento gratuito; el rediseño añade la precisión necesaria de `sin ánimo de lucro`.
- El branding habla de materiales gratuitos y libre acceso; el rediseño lo concreta como `libros PDF`, `audios`, `contenidos libres` y `libre descarga`.
- El branding habla de miembros; el rediseño aclara que la comunidad está conformada por miembros de la ONG.
- El branding habla de filosofía viva y no dogmática; el rediseño lo convierte en mensaje visible desde la home y en garantía del curso.
- El branding habla de unión de dimensiones, contacto extraterrestre y Guías Estelares; el rediseño lo sube al primer impacto para que usuarios y buscadores distingan TSEYOR de una filosofía espiritual genérica.
