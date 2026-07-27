# Informe de coherencia visual y autenticidad de las imágenes de portada

**Fecha:** julio de 2026  
**Ámbito:** `resources/js/Pages/Portada.vue`  
**Decisión principal:** utilizar imágenes sintéticas o ilustraciones para expresar conceptos y aspiraciones; utilizar fotografía real cuando la imagen funcione como evidencia de la comunidad, la ONG o sus actividades.

> **Regla operativa:** símbolo sintético; evidencia real.

## Resumen ejecutivo

La portada mantiene una coherencia estética bastante alta: predominan la serenidad, la naturaleza, la luz cálida, el sentido de comunidad y una representación humana intergeneracional. Sin embargo, existe una diferencia importante entre **coherencia estética** y **credibilidad institucional**.

El principal problema no es que algunas imágenes hayan sido generadas con IA ni que los visitantes puedan detectarlo. El problema es que las seis imágenes fotorealistas de la sección ONG ocupan el lugar narrativo de una prueba documental. Al aparecer junto a expresiones como «La filosofía en acción» y «proyectos concretos», pueden interpretarse como fotografías de miembros y actividades reales de TSEYOR.

La recomendación es:

1. Mantener provisionalmente el hero como imagen aspiracional de marca.
2. Mantener la imagen de la mujer meditando, cuyo uso es metafórico y no documental.
3. Mantener y consolidar el lenguaje ilustrado de Filosofía.
4. Sustituir las seis fotografías IA de la sección ONG por fotografías reales.
5. Si todavía no existe material fotográfico apropiado, usar ilustraciones editoriales claramente no fotográficas.
6. Adelantar alguna evidencia visual real de la comunidad dentro del recorrido inicial.

## Material revisado

El análisis se ha realizado sobre la portada renderizada en `http://localhost`, tanto en escritorio como en móvil, y sobre las imágenes originales o reducidas mediante el parámetro `?w=`.

### Imágenes de portada

- Hero: `/almacen/medios/portada/portada_tseyor_2026_hero.jpg`.
- ONG: `/almacen/medios/portada/ong1.jpg` a `ong6.jpg`.
- Meditación diaria: `/almacen/medios/portada/mujer_meditando.jpg`.
- Filosofía:
  - `el-rayo-sincronizador.jpg`.
  - `La_Confederacion_de_Mundos_Habitados.jpg`.
  - `las-sociedades-armonicas.jpg`.
  - `la-autoobservación.jpg`.
- Fotografías reales mostradas en Comunidad y procedentes de las entradas recientes.

### Documentación contrastada

- `redisenyo_2026/BRANDING.md`.
- `redisenyo_2026/REDISENO.md`.
- `redisenyo_2026/ENFOQUE_MEDITACION.md`.
- `redisenyo_2026/INFORME_UTG_DIVULGACION.md`.

## Diagnóstico general

| Dimensión | Evaluación | Observación principal |
|---|---:|---|
| Coherencia estética | Alta | Las familias comparten humanidad, serenidad, naturaleza y luz cálida. |
| Coherencia emocional | Alta | La portada comunica acompañamiento, apertura y transformación. |
| Coherencia semántica | Media | No siempre queda claro qué imagen simboliza, aspira o documenta. |
| Credibilidad institucional | Vulnerable | El carrusel ONG puede interpretarse como documentación real. |

La secuencia actual presenta primero varias representaciones sintéticas o simbólicas y deja la evidencia humana real para más adelante:

1. Hero fotográfico generado con IA.
2. Representación de los Guías Estelares.
3. Ilustraciones de Filosofía.
4. Fotografías IA de la ONG.
5. Fotografías reales de Comunidad.

Esto provoca que la autenticidad demostrable llegue tarde. La portada comunica muy bien el ideal antes de demostrar suficientemente la realidad.

## Principio de evaluación: función simbólica frente a función probatoria

La misma técnica visual no tiene el mismo efecto en todos los contextos.

| Función de la imagen | Uso legítimo de IA | Criterio |
|---|---:|---|
| Aspiración o estado deseado | Sí, con control | La imagen expresa una posibilidad, no un hecho ocurrido. |
| Concepto abstracto o cosmológico | Sí | El artificio es compatible con la lectura simbólica. |
| Actividad concreta de la ONG | No recomendable | La fotografía se interpreta como prueba de personas y hechos reales. |
| Testimonio, evento o comunidad | No | Debe existir correspondencia entre lo representado y lo ocurrido. |

El riesgo no desaparece porque una imagen sintética resulte indistinguible de una fotografía. Precisamente, cuanto más convincente sea, mayor puede ser la atribución errónea de autenticidad.

## Evaluación por familias visuales

### 1. Hero principal

#### Lo que funciona

- Grupo intergeneracional y diverso.
- Disposición circular que comunica igualdad, escucha y ausencia de jerarquías.
- Naturaleza y horizonte abierto.
- Luz de amanecer asociada a transformación.
- Espacio libre suficiente para integrar el bloque de texto.
- Ausencia de símbolos religiosos o esotéricos cerrados.

La imagen representa eficazmente el ideal de comunidad TSEYOR y coincide con el cambio conceptual descrito en la documentación: pasar de un imaginario cósmico frío y lejano a una experiencia humana, cercana y compartida.

#### Riesgo

La imagen aparece junto a «Somos una ONG». Aunque no muestra una actividad concreta, el visitante puede interpretar que se trata de miembros reales de TSEYOR.

#### Decisión recomendada

**Mantenerla provisionalmente como imagen aspiracional de marca.**

Condiciones:

- No utilizarla como evidencia de una reunión o evento determinado.
- Introducir fotografía real de la comunidad mucho antes en la página.
- Considerar a largo plazo una sesión fotográfica real que reproduzca el concepto: círculo, naturaleza, diversidad generacional, conversación y horizonte.

### 2. Fotografías IA de la ONG

Las seis imágenes representan correctamente distintas áreas de actividad:

| Archivo | Lectura principal |
|---|---|
| `ong1.jpg` | Reunión, coordinación y trabajo organizativo. |
| `ong2.jpg` | Cooperación y actividad comunitaria. |
| `ong3.jpg` | Ayuda humanitaria. |
| `ong4.jpg` | Cultivo y trabajo colectivo. |
| `ong5.jpg` | Meditación grupal. |
| `ong6.jpg` | Hermanamiento y apoyo emocional. |

Su calidad representativa es alta. No obstante, están situadas junto al título «La filosofía en acción» y un texto que habla de proyectos concretos, centros, ayuda humanitaria y acompañamiento. Por ello, el visitante no las recibe únicamente como representación: las recibe como evidencia.

`ong1.jpg` y `ong5.jpg` agravan el problema al incorporar logotipos TSEYOR sintéticos en las escenas. Estos elementos proporcionan una falsa apariencia de procedencia documental. En `ong1.jpg`, además, aparecen materiales impresos ficticios que refuerzan esa lectura.

#### Decisión recomendada

Sustituir el carrusel siguiendo este orden de preferencia:

1. Fotografías reales de actividades de la ONG.
2. Ilustraciones editoriales claramente estilizadas.
3. Fotografía sintética declarada como recreación, solo en casos excepcionales.
4. Evitar fotografía sintética indistinguible de una actividad real.

No se recomienda confiar en que «nadie se dará cuenta». Si el visitante no lo detecta, puede asumir una procedencia falsa; si lo descubre posteriormente, puede extender la duda hacia las fotografías y afirmaciones que sí son reales.

### 3. Ilustraciones de Filosofía

Las cuatro imágenes comparten un lenguaje suficientemente coherente:

- Lápiz de color o grabado ilustrado.
- Textura de papel.
- Paleta terrosa, azul y dorada.
- Atmósfera contemplativa.
- Representación simbólica de conceptos difíciles de fotografiar.

Este tratamiento funciona porque la sección no pretende demostrar acontecimientos. Su función es imaginar, simbolizar y facilitar la comprensión de ideas como el Rayo Sincronizador, la Confederación, las Sociedades Armónicas o la Autoobservación.

#### Ajustes menores

- Unificar mejor las relaciones de aspecto para evitar recortes desiguales dentro de las tarjetas.
- Mantener una paleta y textura compartidas en futuras ilustraciones.
- Evitar que las nuevas piezas se desplacen hacia ciencia ficción espectacular o sensacionalista.

### 4. ¿Debe la ONG usar el mismo estilo de Filosofía?

**No exactamente. Debe compartir el ADN visual de la marca, pero utilizar un dialecto distinto.**

Si ONG y Filosofía utilizaran el mismo lenguaje contemplativo, las actividades de la organización podrían parecer también mundos ideales, posibilidades futuras o conceptos hipotéticos.

#### Lenguaje recomendado para Filosofía

- Simbólico.
- Contemplativo.
- Metafórico.
- Atmosférico.
- Con mayor libertad imaginativa.

#### Lenguaje recomendado para ONG

- Editorial-documental.
- Acciones y procesos reconocibles.
- Personas simplificadas, pero humanas.
- Gestos, manos, herramientas y colaboración.
- Mayor contraste y claridad narrativa.
- Sin imitar una fotografía.
- Sin logotipos falsamente integrados en las escenas.

#### Elementos que pueden compartir

- Paleta azul, dorada y terrosa.
- Texturas orgánicas.
- Tratamiento de línea relacionado.
- Luz cálida.
- Serenidad y dignidad humana.
- Composiciones accesibles y no grandilocuentes.

La relación correcta es: **misma familia; distinto dialecto**.

### 5. Fotografías reales de Comunidad

Son el principal activo de confianza de la portada.

Muestran:

- Lugares y circunstancias específicas.
- Diversidad real de edades, cuerpos y estilos personales.
- Iluminaciones y encuadres imperfectos.
- Encuentros, convivencias y trabajos concretos.
- Uso real de símbolos y materiales TSEYOR.

La variación técnica no debe considerarse automáticamente un defecto. En este contexto, cierta imperfección comunica procedencia, experiencia y realidad. No conviene procesarlas hasta que parezcan una campaña publicitaria homogénea.

#### Mejoras recomendadas

- Mostrar lugares, fechas o títulos de manera visible, no solo mediante `hover`.
- Garantizar que la identificación también esté disponible en móvil.
- Seleccionar algunas fotografías por su valor documental, no únicamente por su calidad estética.
- Incorporar una pequeña muestra real más cerca del hero o sustituir con ellas el carrusel ONG.

### 6. Mujer meditando

La imagen funciona como metáfora aspiracional de introspección y presenta un riesgo bajo:

- No afirma documentar un evento de TSEYOR.
- La postura en silla hace que la práctica resulte accesible.
- Evita el cliché de una postura espectacular de yoga.
- El espacio vacío permite integrar correctamente la interfaz del reproductor.
- La iluminación transmite serenidad y recogimiento.

Su principal debilidad es la proximidad a la estética genérica de bienestar: tonos beige, plantas, luz perfecta y espacio muy controlado. Esto puede hacer que TSEYOR parezca una propuesta de *wellness* convencional, pero no compromete por sí solo la credibilidad institucional.

#### Decisión recomendada

**Mantenerla.** A largo plazo puede sustituirse por una fotografía real cuidadosamente dirigida, pero no tiene la urgencia del carrusel ONG.

#### Incoherencia de contenido relacionada

`ENFOQUE_MEDITACION.md` establece que esta sección debe invitar directamente a la introspección y no hablar de guías ni cosmos. Sin embargo, `Portada.vue` incluye:

> «Meditaciones y reflexiones de nuestros Guías Estelares para el autoconocimiento y el despertar de la consciencia.»

El ajuste más importante en esta sección es, por tanto, revisar el copy antes que sustituir la imagen.

## Sistema visual recomendado

### A. Aspiración de marca

**Aplicación:** hero y, opcionalmente, meditación.

- Puede emplear IA de forma limitada.
- Expresa una posibilidad, una atmósfera o un estado interior.
- No debe atribuir una actividad concreta a personas inexistentes.

### B. Conceptos y cosmología

**Aplicación:** Filosofía y contenidos sobre los Guías Estelares.

- Ilustración simbólica.
- Lenguaje artístico reconocible.
- El artificio forma parte de la lectura y no pretende ser documentación.

### C. Institución y acción

**Aplicación:** ONG, centros, voluntariado y ayuda humanitaria.

- Fotografía real como norma.
- Si no existe material adecuado: ilustración editorial no fotográfica.
- Evitar recreaciones sintéticas que aparenten documentar hechos.

### D. Prueba social

**Aplicación:** Comunidad, blog, testimonios y eventos.

- Personas y acontecimientos reales.
- Autorización de uso cuando corresponda.
- Contexto, fecha y lugar siempre que aporten significado.
- Priorizar especificidad frente a perfección visual.

## Prioridades de actuación

| Prioridad | Actuación | Impacto esperado |
|---:|---|---|
| 1 | Sustituir `ong1.jpg`–`ong6.jpg`. | Elimina el principal riesgo de falsa evidencia. |
| 2 | Incorporar prueba fotográfica real antes en el recorrido. | Refuerza confianza y pertenencia desde el inicio. |
| 3 | Hacer visibles contexto y títulos de Comunidad en móvil. | Convierte las fotografías en información, no decoración. |
| 4 | Revisar el copy de Meditación diaria. | Alinea la implementación con `ENFOQUE_MEDITACION.md`. |
| 5 | Unificar formato y dirección de arte en Filosofía. | Mejora consistencia sin borrar la diversidad temática. |
| 6 | Planificar una futura fotografía real para el hero. | Permite conservar el concepto eliminando el riesgo sintético. |

## Política interna propuesta para imágenes IA

Antes de publicar una imagen, responder:

1. ¿La imagen representa una idea o pretende mostrar algo que ocurrió?
2. ¿Un visitante razonable podría pensar que las personas pertenecen a TSEYOR?
3. ¿La escena contiene logotipos, instalaciones o materiales que aparentan ser reales?
4. ¿Descubrir su origen sintético fortalecería o debilitaría la confianza?
5. ¿Existe fotografía real suficiente, aunque sea menos perfecta?
6. ¿Una ilustración claramente estilizada comunicaría el concepto sin inducir a error?

### Regla de publicación

- Si la imagen **simboliza**, puede ser sintética o ilustrada.
- Si la imagen **demuestra**, debe ser real.
- Si puede confundirse con documentación, no debe publicarse como fotografía sin una razón excepcional y una política de transparencia explícita.

## Criterios de aceptación

- [ ] Ninguna imagen fotorealista sintética aparece como prueba de una actividad de la ONG.
- [ ] La portada muestra personas reales antes de que termine su primera mitad narrativa.
- [ ] Filosofía conserva un lenguaje simbólico reconocible.
- [ ] Las posibles ilustraciones ONG se diferencian claramente de las imágenes contemplativas.
- [ ] Las fotografías reales incluyen contexto accesible también en móvil.
- [ ] Hero y Meditación se entienden como imágenes aspiracionales, no documentales.
- [ ] El copy de Meditación respeta el enfoque de introspección definido en la documentación.
- [ ] Existe una regla interna para decidir y revisar futuros usos de IA.

## Referencias externas

- Nielsen Norman Group, [Photos as Web Content](https://www.nngroup.com/articles/photos-as-web-content/): los usuarios prestan atención a fotografías que aportan información y a personas reales, mientras ignoran con mayor facilidad las imágenes genéricas de relleno.
- Bond, [Artificial authenticity: are NGOs risking their reputation when using AI-generated imagery?](https://www.bond.org.uk/news/2026/03/artificial-authenticity-are-ngos-risking-their-reputation-when-using-ai-generated-imagery/): análisis específico del riesgo reputacional del fotorealismo sintético en comunicaciones de ONG.
- Nightingale y Farid, [AI-synthesized faces are indistinguishable from real faces and more trustworthy](https://pmc.ncbi.nlm.nih.gov/articles/PMC8872790/): evidencia de que los rostros sintéticos pueden resultar difíciles de distinguir de los reales.
- Pawelczyk, Dimmery y Yan, [Implied Authenticity Effect? The Impact of Explicit Labels on AI-Generated Content](https://ojs.aaai.org/index.php/ICWSM/article/view/42721): investigación sobre el efecto de las etiquetas de IA en la autenticidad percibida.

## Conclusión

Las imágenes IA de la ONG no perjudican principalmente porque puedan parecer artificiales. Perjudican porque ocupan el lugar comunicativo de una evidencia que debería corresponder a personas y actividades reales.

El hero y la imagen de meditación pueden conservarse bajo un uso aspiracional controlado. Filosofía puede continuar utilizando ilustraciones simbólicas. La sección ONG, en cambio, debe fundamentarse en fotografía real o, cuando no exista material adecuado, en ilustraciones editoriales inequívocamente no fotográficas.

La portada no necesita elegir entre belleza y autenticidad. Necesita asignar a cada tipo de imagen la función que puede cumplir honestamente.
